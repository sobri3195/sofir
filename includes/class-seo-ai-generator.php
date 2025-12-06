<?php
namespace Sofir\Seo;

class AiGenerator {
    private const OPTION_API_KEY = 'sofir_gemini_api_key';
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    
    private static ?AiGenerator $instance = null;
    private string $api_key = '';

    public static function instance(): AiGenerator {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->api_key = \get_option( self::OPTION_API_KEY, '' );
    }

    public function boot(): void {
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'admin_post_sofir_save_gemini_key', [ $this, 'handle_save_api_key' ] );
        \add_action( 'wp_ajax_sofir_generate_seo_article', [ $this, 'ajax_generate_article' ] );
        \add_action( 'wp_ajax_sofir_research_keywords', [ $this, 'ajax_research_keywords' ] );
        \add_action( 'wp_ajax_sofir_create_post_from_ai', [ $this, 'ajax_create_post' ] );
        \add_action( 'wp_ajax_sofir_get_product_suggestions', [ $this, 'ajax_get_product_suggestions' ] );
    }

    public function get_api_key(): string {
        return $this->api_key;
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/seo-ai/generate',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'rest_generate_article' ],
                'permission_callback' => function () {
                    return \current_user_can( 'edit_posts' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/seo-ai/keywords',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'rest_research_keywords' ],
                'permission_callback' => function () {
                    return \current_user_can( 'edit_posts' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/seo-ai/product-suggestions',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'rest_get_product_suggestions' ],
                'permission_callback' => function () {
                    return \current_user_can( 'edit_posts' );
                },
            ]
        );
    }

    public function handle_save_api_key(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_gemini_settings', '_sofir_nonce' );

        $api_key = isset( $_POST['sofir_gemini_api_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['sofir_gemini_api_key'] ) ) : '';
        
        \update_option( self::OPTION_API_KEY, $api_key );
        $this->api_key = $api_key;

        \wp_safe_redirect( \add_query_arg( 
            [ 
                'page' => 'sofir-dashboard', 
                'tab' => 'seo', 
                'sofir_notice' => 'gemini_saved' 
            ], 
            \admin_url( 'admin.php' ) 
        ) );
        exit;
    }

    public function ajax_generate_article(): void {
        \check_ajax_referer( 'sofir_seo_ai', 'nonce' );

        if ( ! \current_user_can( 'edit_posts' ) ) {
            \wp_send_json_error( [ 'message' => 'Unauthorized' ], 403 );
        }

        $product_list = [];
        if ( ! empty( $_POST['product_list'] ) ) {
            $product_list_json = \wp_unslash( $_POST['product_list'] );
            $product_list = json_decode( $product_list_json, true );
            if ( json_last_error() !== JSON_ERROR_NONE ) {
                $product_list = [];
            }
        }

        $params = [
            'title'           => \sanitize_text_field( $_POST['title'] ?? '' ),
            'keyword'         => \sanitize_text_field( $_POST['keyword'] ?? '' ),
            'article_type'    => \sanitize_text_field( $_POST['article_type'] ?? 'general' ),
            'purpose'         => \sanitize_text_field( $_POST['purpose'] ?? '' ),
            'tone'            => \sanitize_text_field( $_POST['tone'] ?? 'professional' ),
            'word_count'      => (int) ( $_POST['word_count'] ?? 1000 ),
            'pov'             => \sanitize_text_field( $_POST['pov'] ?? 'third_person' ),
            'creativity'      => (float) ( $_POST['creativity'] ?? 0.7 ),
            'readability'     => \sanitize_text_field( $_POST['readability'] ?? 'intermediate' ),
            'include_faq'     => ! empty( $_POST['include_faq'] ),
            'include_toc'     => ! empty( $_POST['include_toc'] ),
            'product_names'   => isset( $_POST['product_names'] ) ? \sanitize_textarea_field( \wp_unslash( $_POST['product_names'] ) ) : '',
            'product_features' => isset( $_POST['product_features'] ) ? \sanitize_textarea_field( \wp_unslash( $_POST['product_features'] ) ) : '',
            'comparison_criteria' => isset( $_POST['comparison_criteria'] ) ? \sanitize_textarea_field( \wp_unslash( $_POST['comparison_criteria'] ) ) : '',
            'list_count'      => (int) ( $_POST['list_count'] ?? 10 ),
            'product_list'    => $product_list,
        ];

        $result = $this->generate_complete_article( $params );

        if ( \is_wp_error( $result ) ) {
            \wp_send_json_error( [ 'message' => $result->get_error_message() ] );
        }

        \wp_send_json_success( $result );
    }

    public function ajax_research_keywords(): void {
        \check_ajax_referer( 'sofir_seo_ai', 'nonce' );

        if ( ! \current_user_can( 'edit_posts' ) ) {
            \wp_send_json_error( [ 'message' => 'Unauthorized' ], 403 );
        }

        $keyword = \sanitize_text_field( $_POST['keyword'] ?? '' );
        
        if ( empty( $keyword ) ) {
            \wp_send_json_error( [ 'message' => 'Keyword is required' ] );
        }

        $keywords = $this->research_keywords( $keyword );

        if ( \is_wp_error( $keywords ) ) {
            \wp_send_json_error( [ 'message' => $keywords->get_error_message() ] );
        }

        \wp_send_json_success( [ 'keywords' => $keywords ] );
    }

    public function ajax_create_post(): void {
        \check_ajax_referer( 'sofir_seo_ai', 'nonce' );

        if ( ! \current_user_can( 'edit_posts' ) ) {
            \wp_send_json_error( [ 'message' => 'Unauthorized' ], 403 );
        }

        $article_json = \wp_unslash( $_POST['article'] ?? '' );
        $status = \sanitize_text_field( $_POST['status'] ?? 'draft' );

        if ( empty( $article_json ) ) {
            \wp_send_json_error( [ 'message' => 'Article data is required' ] );
        }

        $article = json_decode( $article_json, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            \wp_send_json_error( [ 'message' => 'Invalid article data' ] );
        }

        $post_id = $this->create_post_from_article( $article, $status );

        if ( \is_wp_error( $post_id ) ) {
            \wp_send_json_error( [ 'message' => $post_id->get_error_message() ] );
        }

        \wp_send_json_success( [
            'post_id' => $post_id,
            'edit_url' => \get_edit_post_link( $post_id, 'raw' ),
        ] );
    }

    public function rest_generate_article( \WP_REST_Request $request ): \WP_REST_Response {
        $product_list = $request->get_param( 'product_list' );
        if ( is_string( $product_list ) ) {
            $product_list = json_decode( $product_list, true );
            if ( json_last_error() !== JSON_ERROR_NONE ) {
                $product_list = [];
            }
        }

        $params = [
            'title'           => \sanitize_text_field( $request->get_param( 'title' ) ?? '' ),
            'keyword'         => \sanitize_text_field( $request->get_param( 'keyword' ) ?? '' ),
            'article_type'    => \sanitize_text_field( $request->get_param( 'article_type' ) ?? 'general' ),
            'purpose'         => \sanitize_text_field( $request->get_param( 'purpose' ) ?? '' ),
            'tone'            => \sanitize_text_field( $request->get_param( 'tone' ) ?? 'professional' ),
            'word_count'      => (int) ( $request->get_param( 'word_count' ) ?? 1000 ),
            'pov'             => \sanitize_text_field( $request->get_param( 'pov' ) ?? 'third_person' ),
            'creativity'      => (float) ( $request->get_param( 'creativity' ) ?? 0.7 ),
            'readability'     => \sanitize_text_field( $request->get_param( 'readability' ) ?? 'intermediate' ),
            'include_faq'     => (bool) $request->get_param( 'include_faq' ),
            'include_toc'     => (bool) $request->get_param( 'include_toc' ),
            'product_names'   => \sanitize_textarea_field( $request->get_param( 'product_names' ) ?? '' ),
            'product_features' => \sanitize_textarea_field( $request->get_param( 'product_features' ) ?? '' ),
            'comparison_criteria' => \sanitize_textarea_field( $request->get_param( 'comparison_criteria' ) ?? '' ),
            'list_count'      => (int) ( $request->get_param( 'list_count' ) ?? 10 ),
            'product_list'    => $product_list ?? [],
        ];

        $result = $this->generate_complete_article( $params );

        if ( \is_wp_error( $result ) ) {
            return new \WP_REST_Response( 
                [ 'error' => $result->get_error_message() ], 
                400 
            );
        }

        return \rest_ensure_response( $result );
    }

    public function rest_research_keywords( \WP_REST_Request $request ): \WP_REST_Response {
        $keyword = \sanitize_text_field( $request->get_param( 'keyword' ) ?? '' );
        
        if ( empty( $keyword ) ) {
            return new \WP_REST_Response( 
                [ 'error' => 'Keyword is required' ], 
                400 
            );
        }

        $keywords = $this->research_keywords( $keyword );

        if ( \is_wp_error( $keywords ) ) {
            return new \WP_REST_Response( 
                [ 'error' => $keywords->get_error_message() ], 
                400 
            );
        }

        return \rest_ensure_response( [ 'keywords' => $keywords ] );
    }

    private function generate_complete_article( array $params ) {
        if ( empty( $this->api_key ) ) {
            return new \WP_Error( 'no_api_key', 'Google Gemini API key is not configured.' );
        }

        $prompt = $this->build_article_prompt( $params );
        $content = $this->call_gemini_api( $prompt, $params['creativity'] );

        if ( \is_wp_error( $content ) ) {
            return $content;
        }

        $parsed = $this->parse_article_response( $content );
        
        $parsed['seo_score'] = $this->calculate_seo_score( $parsed, $params );
        $parsed['seo_suggestions'] = $this->generate_seo_suggestions( $parsed, $params );
        $parsed['schema'] = $this->generate_article_schema( $parsed, $params );
        $parsed['internal_links'] = $this->suggest_internal_links( $parsed['content'] ?? '' );
        
        return $parsed;
    }

    private function build_article_prompt( array $params ): string {
        $article_type = $params['article_type'] ?? 'general';

        switch ( $article_type ) {
            case 'product_roundup':
                return $this->build_product_roundup_prompt( $params );
            case 'product_review':
                return $this->build_product_review_prompt( $params );
            case 'comparison':
                return $this->build_comparison_prompt( $params );
            case 'listicle':
                return $this->build_listicle_prompt( $params );
            default:
                return $this->build_general_article_prompt( $params );
        }
    }

    private function build_general_article_prompt( array $params ): string {
        $title = $params['title'];
        $keyword = $params['keyword'];
        $purpose = $params['purpose'];
        $tone = $params['tone'];
        $word_count = $params['word_count'];
        $pov = $params['pov'];
        $readability = $params['readability'];

        $pov_text = [
            'first_person' => 'first person (I, we)',
            'second_person' => 'second person (you)',
            'third_person' => 'third person (he, she, they)',
        ][ $pov ] ?? 'third person';

        $prompt = "You are an expert SEO content writer. Create a comprehensive, SEO-optimized article with the following specifications:

**Article Requirements:**
- Title: {$title}
- Primary Keyword: {$keyword}
- Purpose: {$purpose}
- Tone: {$tone}
- Word Count: approximately {$word_count} words
- Point of View: {$pov_text}
- Readability Level: {$readability}

**Content Structure:**
Please provide the article in the following JSON format:

{
  \"title\": \"Optimized article title\",
  \"meta_title\": \"SEO-optimized meta title (55-60 characters)\",
  \"meta_description\": \"Compelling meta description (150-160 characters)\",
  \"slug\": \"url-friendly-slug\",
  \"outline\": [
    \"Introduction\",
    \"Main Point 1\",
    \"Main Point 2\",
    \"Conclusion\"
  ],
  \"introduction\": \"Engaging introduction paragraph that hooks readers\",
  \"content\": \"Full article content with proper HTML formatting including <h2>, <h3>, <p>, <ul>, <ol>, <strong>, <em> tags. Include the primary keyword naturally throughout.\",
  \"headings\": [
    {\"level\": \"h2\", \"text\": \"Main Heading 1\"},
    {\"level\": \"h3\", \"text\": \"Sub Heading 1.1\"}
  ],
  \"talking_points\": [
    \"Key point 1\",
    \"Key point 2\",
    \"Key point 3\"
  ],
  \"contextual_terms\": [
    \"related term 1\",
    \"related term 2\",
    \"LSI keyword 1\"
  ],
  \"conclusion\": \"Strong conclusion paragraph that summarizes key points\",
  \"faqs\": [
    {
      \"question\": \"Common question 1?\",
      \"answer\": \"Detailed answer 1\"
    },
    {
      \"question\": \"Common question 2?\",
      \"answer\": \"Detailed answer 2\"
    }
  ],
  \"featured_image_description\": \"Description of ideal featured image for this article\",
  \"keywords\": [
    \"primary keyword\",
    \"secondary keyword 1\",
    \"secondary keyword 2\"
  ],
  \"inline_suggestions\": [
    \"Suggested related article title 1\",
    \"Suggested related article title 2\"
  ]
}

Make sure the content is:
1. Well-researched and informative
2. Engaging and easy to read
3. SEO-optimized with natural keyword placement
4. Structured with proper headings (H2, H3)
5. Includes lists, examples, and actionable insights
6. Free from grammatical errors
7. Original and plagiarism-free

Return ONLY the JSON response, no additional text.";

        return $prompt;
    }

    private function research_keywords( string $seed_keyword ) {
        if ( empty( $this->api_key ) ) {
            return new \WP_Error( 'no_api_key', 'Google Gemini API key is not configured.' );
        }

        $prompt = "As an SEO keyword research expert, provide a comprehensive keyword analysis for: \"{$seed_keyword}\"

Please return a JSON response with:

{
  \"primary_keyword\": \"{$seed_keyword}\",
  \"search_intent\": \"informational|transactional|navigational|commercial\",
  \"keyword_variations\": [
    {\"keyword\": \"variation 1\", \"difficulty\": \"easy|medium|hard\", \"volume\": \"estimated monthly searches\"},
    {\"keyword\": \"variation 2\", \"difficulty\": \"easy|medium|hard\", \"volume\": \"estimated monthly searches\"}
  ],
  \"long_tail_keywords\": [
    \"long tail keyword 1\",
    \"long tail keyword 2\",
    \"long tail keyword 3\"
  ],
  \"related_keywords\": [
    \"related keyword 1\",
    \"related keyword 2\",
    \"related keyword 3\"
  ],
  \"lsi_keywords\": [
    \"LSI keyword 1\",
    \"LSI keyword 2\",
    \"LSI keyword 3\"
  ],
  \"competitor_keywords\": [
    \"competitor keyword 1\",
    \"competitor keyword 2\"
  ],
  \"question_keywords\": [
    \"how to {$seed_keyword}\",
    \"what is {$seed_keyword}\",
    \"why {$seed_keyword}\"
  ],
  \"trending_topics\": [
    \"trending topic 1 related to {$seed_keyword}\",
    \"trending topic 2 related to {$seed_keyword}\"
  ]
}

Return ONLY the JSON response.";

        $response = $this->call_gemini_api( $prompt, 0.7 );

        if ( \is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( $response, true );
        
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new \WP_Error( 'json_error', 'Failed to parse keyword research response.' );
        }

        return $data;
    }

    private function call_gemini_api( string $prompt, float $temperature = 0.7 ) {
        if ( empty( $this->api_key ) ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                \error_log( '[SOFIR SEO] Gemini API key is not configured' );
            }
            return new \WP_Error( 'no_api_key', 'Google Gemini API key tidak dikonfigurasi. Silakan masukkan API key di tab SEO.' );
        }

        $url = self::GEMINI_API_URL . '?key=' . $this->api_key;

        $body = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => 8000,
                'topP' => 0.95,
                'topK' => 40,
            ],
        ];

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            \error_log( sprintf( '[SOFIR SEO] Calling Gemini API - URL: %s, Temperature: %.2f', $url, $temperature ) );
        }

        $response = \wp_remote_post(
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body'    => \wp_json_encode( $body ),
                'timeout' => 60,
            ]
        );

        if ( \is_wp_error( $response ) ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                \error_log( sprintf( '[SOFIR SEO] API request error: %s', $response->get_error_message() ) );
            }
            return new \WP_Error( 
                'api_connection_error', 
                'Gagal terhubung ke Google Gemini API: ' . $response->get_error_message() 
            );
        }

        $code = \wp_remote_retrieve_response_code( $response );
        
        if ( $code !== 200 ) {
            $body = \wp_remote_retrieve_body( $response );
            $error = json_decode( $body, true );
            $message = $error['error']['message'] ?? 'API request failed';
            
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                \error_log( sprintf( '[SOFIR SEO] API error (Code %d): %s', $code, $message ) );
                \error_log( '[SOFIR SEO] Response body: ' . $body );
            }
            
            if ( $code === 400 && strpos( $message, 'API_KEY_INVALID' ) !== false ) {
                $message = 'API key tidak valid. Silakan periksa kembali API key Anda di tab SEO.';
            } elseif ( $code === 429 ) {
                $message = 'Rate limit tercapai. Silakan coba lagi beberapa saat.';
            } elseif ( $code === 403 ) {
                $message = 'API key tidak memiliki akses. Pastikan API key Anda memiliki izin untuk Generative Language API.';
            }
            
            return new \WP_Error( 'api_error', $message . ' (HTTP ' . $code . ')' );
        }

        $body = \wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                \error_log( '[SOFIR SEO] JSON decode error: ' . json_last_error_msg() );
            }
            return new \WP_Error( 'json_error', 'Gagal mem-parse response dari API: ' . json_last_error_msg() );
        }

        if ( empty( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                \error_log( '[SOFIR SEO] Empty response from Gemini API' );
                \error_log( '[SOFIR SEO] Full response: ' . print_r( $data, true ) );
            }
            return new \WP_Error( 'empty_response', 'Response kosong dari Gemini API. Silakan coba lagi.' );
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'];
        
        $text = preg_replace( '/```json\s*/', '', $text );
        $text = preg_replace( '/```\s*$/', '', $text );
        $text = trim( $text );

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            \error_log( sprintf( '[SOFIR SEO] API response received - Length: %d characters', strlen( $text ) ) );
        }

        return $text;
    }

    private function parse_article_response( string $content ): array {
        $data = json_decode( $content, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return [
                'title'       => 'Generated Article',
                'content'     => $content,
                'error'       => 'Failed to parse JSON response',
            ];
        }

        return array_merge(
            [
                'title'                      => '',
                'meta_title'                 => '',
                'meta_description'           => '',
                'slug'                       => '',
                'outline'                    => [],
                'introduction'               => '',
                'content'                    => '',
                'headings'                   => [],
                'talking_points'             => [],
                'contextual_terms'           => [],
                'conclusion'                 => '',
                'faqs'                       => [],
                'featured_image_description' => '',
                'keywords'                   => [],
                'inline_suggestions'         => [],
            ],
            $data
        );
    }

    private function calculate_seo_score( array $article, array $params ): array {
        $score = 0;
        $max_score = 100;
        $checks = [];

        $content = $article['content'] ?? '';
        $title = $article['title'] ?? '';
        $meta_desc = $article['meta_description'] ?? '';
        $keyword = $params['keyword'] ?? '';

        if ( ! empty( $title ) && strlen( $title ) >= 30 && strlen( $title ) <= 70 ) {
            $score += 10;
            $checks[] = [ 'item' => 'Title length', 'status' => 'pass', 'points' => 10 ];
        } else {
            $checks[] = [ 'item' => 'Title length', 'status' => 'fail', 'points' => 0 ];
        }

        if ( ! empty( $keyword ) && stripos( $title, $keyword ) !== false ) {
            $score += 15;
            $checks[] = [ 'item' => 'Keyword in title', 'status' => 'pass', 'points' => 15 ];
        } else {
            $checks[] = [ 'item' => 'Keyword in title', 'status' => 'fail', 'points' => 0 ];
        }

        if ( ! empty( $meta_desc ) && strlen( $meta_desc ) >= 120 && strlen( $meta_desc ) <= 160 ) {
            $score += 10;
            $checks[] = [ 'item' => 'Meta description length', 'status' => 'pass', 'points' => 10 ];
        } else {
            $checks[] = [ 'item' => 'Meta description length', 'status' => 'fail', 'points' => 0 ];
        }

        if ( ! empty( $keyword ) && stripos( $meta_desc, $keyword ) !== false ) {
            $score += 10;
            $checks[] = [ 'item' => 'Keyword in meta description', 'status' => 'pass', 'points' => 10 ];
        } else {
            $checks[] = [ 'item' => 'Keyword in meta description', 'status' => 'fail', 'points' => 0 ];
        }

        $word_count = str_word_count( strip_tags( $content ) );
        if ( $word_count >= 800 ) {
            $score += 15;
            $checks[] = [ 'item' => 'Content length (' . $word_count . ' words)', 'status' => 'pass', 'points' => 15 ];
        } else {
            $checks[] = [ 'item' => 'Content length (' . $word_count . ' words)', 'status' => 'fail', 'points' => 0 ];
        }

        if ( ! empty( $keyword ) ) {
            $keyword_count = substr_count( strtolower( $content ), strtolower( $keyword ) );
            $keyword_density = ( $word_count > 0 ) ? ( $keyword_count / $word_count ) * 100 : 0;
            
            if ( $keyword_density >= 0.5 && $keyword_density <= 2.5 ) {
                $score += 10;
                $checks[] = [ 'item' => 'Keyword density (' . round( $keyword_density, 2 ) . '%)', 'status' => 'pass', 'points' => 10 ];
            } else {
                $checks[] = [ 'item' => 'Keyword density (' . round( $keyword_density, 2 ) . '%)', 'status' => 'fail', 'points' => 0 ];
            }
        }

        $h2_count = substr_count( $content, '<h2' );
        $h3_count = substr_count( $content, '<h3' );
        
        if ( $h2_count >= 2 ) {
            $score += 10;
            $checks[] = [ 'item' => 'Heading structure (' . $h2_count . ' H2s)', 'status' => 'pass', 'points' => 10 ];
        } else {
            $checks[] = [ 'item' => 'Heading structure (' . $h2_count . ' H2s)', 'status' => 'fail', 'points' => 0 ];
        }

        if ( ! empty( $article['faqs'] ) && is_array( $article['faqs'] ) ) {
            $score += 10;
            $checks[] = [ 'item' => 'FAQ section included', 'status' => 'pass', 'points' => 10 ];
        } else {
            $checks[] = [ 'item' => 'FAQ section included', 'status' => 'fail', 'points' => 0 ];
        }

        if ( ! empty( $article['introduction'] ) ) {
            $score += 5;
            $checks[] = [ 'item' => 'Introduction present', 'status' => 'pass', 'points' => 5 ];
        } else {
            $checks[] = [ 'item' => 'Introduction present', 'status' => 'fail', 'points' => 0 ];
        }

        if ( ! empty( $article['conclusion'] ) ) {
            $score += 5;
            $checks[] = [ 'item' => 'Conclusion present', 'status' => 'pass', 'points' => 5 ];
        } else {
            $checks[] = [ 'item' => 'Conclusion present', 'status' => 'fail', 'points' => 0 ];
        }

        return [
            'score' => min( $score, $max_score ),
            'max_score' => $max_score,
            'percentage' => round( ( $score / $max_score ) * 100 ),
            'checks' => $checks,
        ];
    }

    private function generate_seo_suggestions( array $article, array $params ): array {
        $suggestions = [];
        $content = $article['content'] ?? '';
        $keyword = $params['keyword'] ?? '';

        $word_count = str_word_count( strip_tags( $content ) );
        
        if ( $word_count < 800 ) {
            $suggestions[] = [
                'type' => 'warning',
                'message' => 'Content is shorter than recommended. Aim for at least 800-1000 words for better SEO.',
            ];
        }

        if ( ! empty( $keyword ) ) {
            $keyword_count = substr_count( strtolower( $content ), strtolower( $keyword ) );
            
            if ( $keyword_count === 0 ) {
                $suggestions[] = [
                    'type' => 'error',
                    'message' => 'Primary keyword not found in content. Add it naturally throughout the article.',
                ];
            } elseif ( $keyword_count < 3 ) {
                $suggestions[] = [
                    'type' => 'warning',
                    'message' => 'Primary keyword appears only ' . $keyword_count . ' time(s). Consider using it more naturally.',
                ];
            }
        }

        if ( strpos( $content, '<img' ) === false ) {
            $suggestions[] = [
                'type' => 'info',
                'message' => 'Add images to break up text and improve user engagement.',
            ];
        }

        if ( empty( $article['faqs'] ) ) {
            $suggestions[] = [
                'type' => 'info',
                'message' => 'Adding an FAQ section can help capture featured snippets in search results.',
            ];
        }

        $external_links = substr_count( $content, 'href=' ) - substr_count( $content, home_url() );
        
        if ( $external_links === 0 ) {
            $suggestions[] = [
                'type' => 'info',
                'message' => 'Consider adding 2-3 external links to authoritative sources to boost credibility.',
            ];
        }

        if ( empty( $suggestions ) ) {
            $suggestions[] = [
                'type' => 'success',
                'message' => 'Great job! Your article follows SEO best practices.',
            ];
        }

        return $suggestions;
    }

    private function generate_article_schema( array $article, array $params ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['title'] ?? '',
            'description' => $article['meta_description'] ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => \get_the_author_meta( 'display_name', \get_current_user_id() ),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => \get_bloginfo( 'name' ),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => \get_site_icon_url(),
                ],
            ],
            'datePublished' => current_time( 'c' ),
            'dateModified' => current_time( 'c' ),
        ];

        if ( ! empty( $article['keywords'] ) ) {
            $schema['keywords'] = implode( ', ', $article['keywords'] );
        }

        if ( ! empty( $article['faqs'] ) && is_array( $article['faqs'] ) ) {
            $faq_schema = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => [],
            ];

            foreach ( $article['faqs'] as $faq ) {
                if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
                    $faq_schema['mainEntity'][] = [
                        '@type' => 'Question',
                        'name' => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['answer'],
                        ],
                    ];
                }
            }

            return [
                'article' => $schema,
                'faq' => $faq_schema,
            ];
        }

        return [ 'article' => $schema ];
    }

    private function suggest_internal_links( string $content ): array {
        $suggestions = [];

        $recent_posts = \get_posts( [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
        ] );

        $content_lower = strtolower( strip_tags( $content ) );

        foreach ( $recent_posts as $post ) {
            $post_title_lower = strtolower( $post->post_title );
            $words = explode( ' ', $post_title_lower );
            
            $matches = 0;
            foreach ( $words as $word ) {
                if ( strlen( $word ) > 4 && stripos( $content_lower, $word ) !== false ) {
                    $matches++;
                }
            }

            if ( $matches >= 2 ) {
                $suggestions[] = [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'url' => \get_permalink( $post->ID ),
                    'relevance' => $matches,
                ];
            }
        }

        usort( $suggestions, function( $a, $b ) {
            return $b['relevance'] - $a['relevance'];
        } );

        return array_slice( $suggestions, 0, 5 );
    }

    public function create_post_from_article( array $article, string $status = 'draft' ): int {
        $content = '';

        if ( ! empty( $article['introduction'] ) ) {
            $content .= '<p>' . $article['introduction'] . '</p>';
        }

        if ( ! empty( $article['outline'] ) && ! empty( $article['include_toc'] ) ) {
            $content .= '<div class="table-of-contents"><h2>Table of Contents</h2><ul>';
            foreach ( $article['outline'] as $item ) {
                $slug = sanitize_title( $item );
                $content .= '<li><a href="#' . $slug . '">' . esc_html( $item ) . '</a></li>';
            }
            $content .= '</ul></div>';
        }

        $content .= $article['content'] ?? '';

        if ( ! empty( $article['conclusion'] ) ) {
            $content .= '<h2>Conclusion</h2><p>' . $article['conclusion'] . '</p>';
        }

        if ( ! empty( $article['faqs'] ) && is_array( $article['faqs'] ) ) {
            $content .= '<h2>Frequently Asked Questions</h2><div class="faq-section">';
            foreach ( $article['faqs'] as $faq ) {
                if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
                    $content .= '<div class="faq-item">';
                    $content .= '<h3>' . esc_html( $faq['question'] ) . '</h3>';
                    $content .= '<p>' . esc_html( $faq['answer'] ) . '</p>';
                    $content .= '</div>';
                }
            }
            $content .= '</div>';
        }

        $post_data = [
            'post_title'   => $article['title'] ?? 'AI Generated Article',
            'post_content' => $content,
            'post_status'  => $status,
            'post_type'    => 'post',
            'post_name'    => $article['slug'] ?? sanitize_title( $article['title'] ?? '' ),
        ];

        $post_id = \wp_insert_post( $post_data );

        if ( $post_id && ! \is_wp_error( $post_id ) ) {
            if ( ! empty( $article['meta_title'] ) ) {
                \update_post_meta( $post_id, 'sofir_seo_title', $article['meta_title'] );
            }
            
            if ( ! empty( $article['meta_description'] ) ) {
                \update_post_meta( $post_id, 'sofir_seo_description', $article['meta_description'] );
            }
            
            if ( ! empty( $article['keywords'] ) ) {
                \update_post_meta( $post_id, 'sofir_seo_keywords', implode( ', ', $article['keywords'] ) );
            }

            if ( ! empty( $article['schema'] ) ) {
                \update_post_meta( $post_id, 'sofir_article_schema', $article['schema'] );
            }

            if ( ! empty( $article['seo_score'] ) ) {
                \update_post_meta( $post_id, 'sofir_seo_score', $article['seo_score'] );
            }
        }

        return $post_id;
    }

    private function build_product_roundup_prompt( array $params ): string {
        $title = $params['title'];
        $keyword = $params['keyword'];
        $tone = $params['tone'];
        $word_count = $params['word_count'];
        $pov = $params['pov'];
        $readability = $params['readability'];
        $product_names = $params['product_names'] ?? '';
        $product_features = $params['product_features'] ?? '';
        $product_list = $params['product_list'] ?? [];

        $pov_text = [
            'first_person' => 'first person (I, we)',
            'second_person' => 'second person (you)',
            'third_person' => 'third person (he, she, they)',
        ][ $pov ] ?? 'third person';

        $products_info = '';
        if ( ! empty( $product_list ) ) {
            $products_info = "\n\n**Products to Review:**\n";
            foreach ( $product_list as $index => $product ) {
                $num = $index + 1;
                $products_info .= "{$num}. {$product['name']}";
                if ( ! empty( $product['url'] ) ) {
                    $products_info .= " (URL: {$product['url']})";
                }
                if ( ! empty( $product['description'] ) ) {
                    $products_info .= " - {$product['description']}";
                }
                $products_info .= "\n";
            }
        } elseif ( ! empty( $product_names ) ) {
            $products_info = "\n- Products to Include: {$product_names}";
        }

        $features_info = ! empty( $product_features ) ? "\n- Key Features to Compare: {$product_features}" : '';

        $prompt = "You are an expert affiliate marketer and product reviewer. Create a comprehensive PRODUCT ROUNDUP article with the following specifications:

**Article Type:** Product Roundup (comparing multiple products in one category)

**Article Requirements:**
- Title: {$title}
- Primary Keyword: {$keyword}
- Tone: {$tone}
- Word Count: approximately {$word_count} words
- Point of View: {$pov_text}
- Readability Level: {$readability}{$products_info}{$features_info}

**Product Roundup Structure:**
For each product in the roundup, include:
1. Product name and brief introduction
2. Key features and specifications
3. Pros and cons
4. Best use case or ideal customer
5. Pricing information (if available)
6. Overall rating or recommendation
7. Affiliate-friendly call-to-action

**Content Requirements:**
- Start with an engaging introduction explaining what readers will learn
- Create a comparison table (in HTML) summarizing all products
- Dedicate a detailed section for each product
- Include a 'How We Chose' or methodology section
- Add a buying guide section
- Conclude with final recommendations for different user types
- Natural keyword placement throughout
- Use H2 for product names, H3 for subsections

Please provide the article in the following JSON format:

{
  \"title\": \"Optimized article title\",
  \"meta_title\": \"SEO-optimized meta title (55-60 characters)\",
  \"meta_description\": \"Compelling meta description (150-160 characters)\",
  \"slug\": \"url-friendly-slug\",
  \"outline\": [
    \"Introduction\",
    \"Product 1 Name - Full Review\",
    \"Product 2 Name - Full Review\",
    \"Comparison Table\",
    \"How We Chose These Products\",
    \"Buying Guide\",
    \"Final Recommendations\",
    \"Conclusion\"
  ],
  \"introduction\": \"Engaging introduction paragraph\",
  \"content\": \"Full article content with proper HTML formatting including comparison tables, product sections with <h2>, <h3>, <ul>, <ol>, <table>, <strong>, <em> tags\",
  \"headings\": [
    {\"level\": \"h2\", \"text\": \"Product Name\"},
    {\"level\": \"h3\", \"text\": \"Pros and Cons\"}
  ],
  \"talking_points\": [
    \"Key comparison point 1\",
    \"Key comparison point 2\"
  ],
  \"contextual_terms\": [
    \"product-related term 1\",
    \"LSI keyword 1\"
  ],
  \"conclusion\": \"Strong conclusion with final recommendations\",
  \"faqs\": [
    {
      \"question\": \"Which product is best for [use case]?\",
      \"answer\": \"Detailed answer\"
    }
  ],
  \"featured_image_description\": \"Description of ideal featured image\",
  \"keywords\": [
    \"primary keyword\",
    \"secondary keyword 1\"
  ],
  \"inline_suggestions\": [
    \"Related product review title 1\"
  ]
}

Return ONLY the JSON response, no additional text.";

        return $prompt;
    }

    private function build_product_review_prompt( array $params ): string {
        $title = $params['title'];
        $keyword = $params['keyword'];
        $tone = $params['tone'];
        $word_count = $params['word_count'];
        $pov = $params['pov'];
        $readability = $params['readability'];
        $product_name = $params['product_names'] ?? '';
        $product_list = $params['product_list'] ?? [];

        $pov_text = [
            'first_person' => 'first person (I, we)',
            'second_person' => 'second person (you)',
            'third_person' => 'third person (he, she, they)',
        ][ $pov ] ?? 'third person';

        $product_info = '';
        if ( ! empty( $product_list ) && isset( $product_list[0] ) ) {
            $product = $product_list[0];
            $product_info = "\n\n**Product to Review:**\n";
            $product_info .= "- Name: {$product['name']}\n";
            if ( ! empty( $product['url'] ) ) {
                $product_info .= "- URL: {$product['url']}\n";
            }
            if ( ! empty( $product['description'] ) ) {
                $product_info .= "- Description: {$product['description']}\n";
            }
        } elseif ( ! empty( $product_name ) ) {
            $product_info = "\n- Product Name: {$product_name}";
        }

        $prompt = "You are an expert product reviewer. Create a comprehensive IN-DEPTH PRODUCT REVIEW with the following specifications:

**Article Type:** Single Product Review (detailed analysis of one product)

**Article Requirements:**
- Title: {$title}
- Primary Keyword: {$keyword}
- Tone: {$tone}
- Word Count: approximately {$word_count} words
- Point of View: {$pov_text}
- Readability Level: {$readability}{$product_info}

**Product Review Structure:**
1. Product introduction and first impressions
2. Detailed specifications and features
3. Design and build quality
4. Performance and real-world testing
5. Comprehensive pros and cons list
6. Comparison with competitors
7. Who should buy this product
8. Overall rating and final verdict
9. Where to buy (with affiliate-friendly CTA)

**Content Requirements:**
- Start with a compelling hook about the product
- Include a specifications table
- Use H2 for major sections, H3 for subsections
- Add a pros/cons box
- Include rating scores (overall, features, value, etc.)
- Natural keyword placement
- Honest, balanced review with both positives and negatives
- Call-to-action for purchase

Please provide the article in the following JSON format:

{
  \"title\": \"Optimized article title\",
  \"meta_title\": \"SEO-optimized meta title (55-60 characters)\",
  \"meta_description\": \"Compelling meta description (150-160 characters)\",
  \"slug\": \"url-friendly-slug\",
  \"outline\": [
    \"Introduction\",
    \"Product Overview\",
    \"Key Features\",
    \"Performance Testing\",
    \"Pros and Cons\",
    \"Comparison with Alternatives\",
    \"Who Should Buy\",
    \"Final Verdict\"
  ],
  \"introduction\": \"Engaging introduction paragraph\",
  \"content\": \"Full article content with proper HTML formatting including specs table, pros/cons lists, rating boxes with <h2>, <h3>, <table>, <ul>, <ol>, <strong>, <em> tags\",
  \"headings\": [
    {\"level\": \"h2\", \"text\": \"Product Overview\"},
    {\"level\": \"h3\", \"text\": \"Design Quality\"}
  ],
  \"talking_points\": [
    \"Key feature 1\",
    \"Performance insight 2\"
  ],
  \"contextual_terms\": [
    \"product-related term 1\",
    \"LSI keyword 1\"
  ],
  \"conclusion\": \"Strong conclusion with purchase recommendation\",
  \"faqs\": [
    {
      \"question\": \"Is this product worth the price?\",
      \"answer\": \"Detailed answer\"
    }
  ],
  \"featured_image_description\": \"Description of ideal featured image\",
  \"keywords\": [
    \"primary keyword\",
    \"secondary keyword 1\"
  ],
  \"inline_suggestions\": [
    \"Related product review title 1\"
  ]
}

Return ONLY the JSON response, no additional text.";

        return $prompt;
    }

    private function build_comparison_prompt( array $params ): string {
        $title = $params['title'];
        $keyword = $params['keyword'];
        $tone = $params['tone'];
        $word_count = $params['word_count'];
        $pov = $params['pov'];
        $readability = $params['readability'];
        $product_names = $params['product_names'] ?? '';
        $comparison_criteria = $params['comparison_criteria'] ?? '';
        $product_list = $params['product_list'] ?? [];

        $pov_text = [
            'first_person' => 'first person (I, we)',
            'second_person' => 'second person (you)',
            'third_person' => 'third person (he, she, they)',
        ][ $pov ] ?? 'third person';

        $products_info = '';
        if ( ! empty( $product_list ) ) {
            $products_info = "\n\n**Products to Compare:**\n";
            foreach ( $product_list as $index => $product ) {
                $num = $index + 1;
                $products_info .= "{$num}. {$product['name']}";
                if ( ! empty( $product['url'] ) ) {
                    $products_info .= " (URL: {$product['url']})";
                }
                if ( ! empty( $product['description'] ) ) {
                    $products_info .= " - {$product['description']}";
                }
                $products_info .= "\n";
            }
        } elseif ( ! empty( $product_names ) ) {
            $products_info = "\n- Products to Compare: {$product_names}";
        }

        $criteria_info = ! empty( $comparison_criteria ) ? "\n- Comparison Criteria: {$comparison_criteria}" : '';

        $prompt = "You are an expert product analyst. Create a detailed HEAD-TO-HEAD COMPARISON article with the following specifications:

**Article Type:** Product Comparison (detailed comparison of 2-3 products)

**Article Requirements:**
- Title: {$title}
- Primary Keyword: {$keyword}
- Tone: {$tone}
- Word Count: approximately {$word_count} words
- Point of View: {$pov_text}
- Readability Level: {$readability}{$products_info}{$criteria_info}

**Comparison Structure:**
1. Introduction explaining what's being compared and why
2. Quick overview of each product
3. Side-by-side comparison table
4. Detailed comparison across multiple criteria:
   - Features and specifications
   - Performance and quality
   - Price and value
   - User experience
   - Support and warranty
5. Winner declaration for each category
6. Final verdict and recommendations
7. Which product is best for different user types

**Content Requirements:**
- Start with clear comparison purpose
- Use HTML comparison tables
- Declare a winner for each criterion
- Be objective and data-driven
- Use H2 for comparison criteria, H3 for sub-criteria
- Include a final scorecard or rating summary
- Natural keyword placement
- Affiliate-friendly calls-to-action

Please provide the article in the following JSON format:

{
  \"title\": \"Optimized article title\",
  \"meta_title\": \"SEO-optimized meta title (55-60 characters)\",
  \"meta_description\": \"Compelling meta description (150-160 characters)\",
  \"slug\": \"url-friendly-slug\",
  \"outline\": [
    \"Introduction\",
    \"Product A Overview\",
    \"Product B Overview\",
    \"Quick Comparison Table\",
    \"Features Comparison\",
    \"Performance Comparison\",
    \"Price Comparison\",
    \"Final Verdict\"
  ],
  \"introduction\": \"Engaging introduction paragraph\",
  \"content\": \"Full article content with proper HTML formatting including comparison tables, winner declarations with <h2>, <h3>, <table>, <ul>, <ol>, <strong>, <em> tags\",
  \"headings\": [
    {\"level\": \"h2\", \"text\": \"Feature Comparison\"},
    {\"level\": \"h3\", \"text\": \"Winner: Product A\"}
  ],
  \"talking_points\": [
    \"Key difference 1\",
    \"Key difference 2\"
  ],
  \"contextual_terms\": [
    \"comparison-related term 1\",
    \"LSI keyword 1\"
  ],
  \"conclusion\": \"Strong conclusion with clear winner or recommendation\",
  \"faqs\": [
    {
      \"question\": \"Which product is better for [use case]?\",
      \"answer\": \"Detailed answer\"
    }
  ],
  \"featured_image_description\": \"Description of ideal featured image\",
  \"keywords\": [
    \"primary keyword\",
    \"secondary keyword 1\"
  ],
  \"inline_suggestions\": [
    \"Related comparison title 1\"
  ]
}

Return ONLY the JSON response, no additional text.";

        return $prompt;
    }

    private function build_listicle_prompt( array $params ): string {
        $title = $params['title'];
        $keyword = $params['keyword'];
        $tone = $params['tone'];
        $word_count = $params['word_count'];
        $pov = $params['pov'];
        $readability = $params['readability'];
        $list_count = $params['list_count'] ?? 10;

        $pov_text = [
            'first_person' => 'first person (I, we)',
            'second_person' => 'second person (you)',
            'third_person' => 'third person (he, she, they)',
        ][ $pov ] ?? 'third person';

        $prompt = "You are an expert content writer specializing in viral listicles. Create an engaging LISTICLE article with the following specifications:

**Article Type:** Listicle (numbered or bulleted list article)

**Article Requirements:**
- Title: {$title}
- Primary Keyword: {$keyword}
- Tone: {$tone}
- Word Count: approximately {$word_count} words
- Point of View: {$pov_text}
- Readability Level: {$readability}
- Number of Items: {$list_count}

**Listicle Structure:**
1. Compelling introduction explaining the list's value
2. {$list_count} main list items, each with:
   - Catchy subheading
   - Detailed explanation (100-200 words per item)
   - Supporting details or examples
   - Optional image suggestion
3. Brief conclusion summarizing the list
4. Call-to-action

**Content Requirements:**
- Use H2 for each list item (e.g., \"1. First Item Name\")
- Start with the most important/compelling items
- Each item should provide real value
- Include actionable tips or insights
- Use engaging, scannable formatting
- Add numbers to H2 headings (1., 2., 3., etc.)
- Natural keyword placement
- Make it shareable and click-worthy

Please provide the article in the following JSON format:

{
  \"title\": \"Optimized article title with number (e.g., '10 Best...')\",
  \"meta_title\": \"SEO-optimized meta title (55-60 characters)\",
  \"meta_description\": \"Compelling meta description (150-160 characters)\",
  \"slug\": \"url-friendly-slug\",
  \"outline\": [
    \"Introduction\",
    \"1. First Item\",
    \"2. Second Item\",
    \"3. Third Item\",
    \"Conclusion\"
  ],
  \"introduction\": \"Engaging introduction paragraph that hooks readers\",
  \"content\": \"Full article content with proper HTML formatting, numbered H2 headings for each list item with <h2>, <h3>, <ul>, <ol>, <strong>, <em> tags\",
  \"headings\": [
    {\"level\": \"h2\", \"text\": \"1. First Item Name\"},
    {\"level\": \"h3\", \"text\": \"Why This Matters\"}
  ],
  \"talking_points\": [
    \"Key point from item 1\",
    \"Key point from item 2\"
  ],
  \"contextual_terms\": [
    \"list-related term 1\",
    \"LSI keyword 1\"
  ],
  \"conclusion\": \"Strong conclusion summarizing the list value\",
  \"faqs\": [
    {
      \"question\": \"What is the best [item] on this list?\",
      \"answer\": \"Detailed answer\"
    }
  ],
  \"featured_image_description\": \"Description of ideal featured image\",
  \"keywords\": [
    \"primary keyword\",
    \"secondary keyword 1\"
  ],
  \"inline_suggestions\": [
    \"Related listicle title 1\"
  ]
}

Return ONLY the JSON response, no additional text.";

        return $prompt;
    }

    public function ajax_get_product_suggestions(): void {
        \check_ajax_referer( 'sofir_seo_ai', 'nonce' );

        if ( ! \current_user_can( 'edit_posts' ) ) {
            \wp_send_json_error( [ 'message' => 'Unauthorized' ], 403 );
        }

        $query = \sanitize_text_field( $_POST['query'] ?? '' );
        
        if ( empty( $query ) ) {
            \wp_send_json_error( [ 'message' => 'Search query is required' ] );
        }

        $products = $this->fetch_product_suggestions( $query );

        if ( \is_wp_error( $products ) ) {
            \wp_send_json_error( [ 'message' => $products->get_error_message() ] );
        }

        \wp_send_json_success( [ 'products' => $products ] );
    }

    public function rest_get_product_suggestions( \WP_REST_Request $request ): \WP_REST_Response {
        $query = \sanitize_text_field( $request->get_param( 'query' ) ?? '' );
        
        if ( empty( $query ) ) {
            return new \WP_REST_Response( 
                [ 'error' => 'Search query is required' ], 
                400 
            );
        }

        $products = $this->fetch_product_suggestions( $query );

        if ( \is_wp_error( $products ) ) {
            return new \WP_REST_Response( 
                [ 'error' => $products->get_error_message() ], 
                400 
            );
        }

        return \rest_ensure_response( [ 'products' => $products ] );
    }

    private function fetch_product_suggestions( string $query ): array|\WP_Error {
        if ( empty( $this->api_key ) ) {
            return new \WP_Error( 'no_api_key', 'Google Gemini API key is not configured. Using AI to generate product suggestions instead.' );
        }

        $prompt = "You are a product research expert. Based on the search query: \"{$query}\", suggest 5-10 popular and relevant products that would be perfect for a product roundup or review article.

For each product, provide:
1. Product name (real, popular products if possible)
2. A typical product URL structure (you can use placeholder domain like 'example.com/product-name')
3. A brief 1-2 sentence description highlighting key features

Return ONLY a JSON response in this format:

{
  \"products\": [
    {
      \"name\": \"Product Name\",
      \"url\": \"https://example.com/product-name\",
      \"description\": \"Brief description of key features and benefits\"
    }
  ]
}

Focus on well-known, high-quality products that readers would actually want to know about. Return ONLY the JSON, no additional text.";

        $response = $this->call_gemini_api( $prompt, 0.7 );

        if ( \is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( $response, true );
        
        if ( json_last_error() !== JSON_ERROR_NONE || empty( $data['products'] ) ) {
            return new \WP_Error( 'json_error', 'Failed to parse product suggestions response.' );
        }

        return $data['products'];
    }
}
