# SEO AI Article Generator - Documentation

## Overview

The SEO AI Article Generator is a powerful feature in SOFIR that uses Google's Gemini AI to create comprehensive, SEO-optimized articles automatically. This feature helps content creators generate high-quality articles with proper structure, keywords, and SEO optimization.

## Features

### 🤖 AI-Powered Article Generation

Generate complete articles with:
- **SEO-optimized content** - Properly structured with keywords
- **Multiple content sections** - Introduction, body, conclusion, FAQs
- **Heading structure** - Automatic H2, H3, H4 hierarchy
- **Meta data** - Auto-generated meta title and description
- **Keyword optimization** - Natural keyword placement
- **Internal linking suggestions** - Relevant post recommendations
- **Schema markup** - JSON-LD structured data

### 🎯 Customization Options

1. **Article Settings:**
   - Article Title - Main topic or title
   - Target Keyword - Primary SEO keyword
   - Article Purpose - Informational, educational, transactional, review, how-to, listicle
   - Tone - Professional, casual, friendly, authoritative, conversational, technical
   - Word Count - 300-5000 words
   
2. **Writing Style:**
   - Point of View - First person, second person, third person
   - Readability Level - Beginner, intermediate, advanced
   - Creativity Level - Adjustable slider (0.0 - 1.0)
   
3. **Content Options:**
   - Include FAQ Section - Checkbox
   - Include Table of Contents - Checkbox

### 📊 SEO Analysis

The generator provides comprehensive SEO scoring:

- **Real-time SEO Score** (0-100)
- **SEO Checklist:**
  - Title length (30-70 characters)
  - Keyword in title
  - Meta description length (120-160 characters)
  - Keyword in meta description
  - Content length (800+ words recommended)
  - Keyword density (0.5-2.5% optimal)
  - Heading structure (2+ H2 headings)
  - FAQ section presence
  - Introduction & conclusion presence

### 🔍 Keyword Research Tool

Built-in keyword research feature provides:

- **Keyword Variations** - Alternative keyword suggestions with difficulty and volume
- **Long-Tail Keywords** - Specific, targeted keyword phrases
- **Related Keywords** - Semantically related terms
- **LSI Keywords** - Latent Semantic Indexing keywords
- **Competitor Keywords** - Keywords used by competitors
- **Question Keywords** - Common questions about the topic
- **Trending Topics** - Current trending topics related to your keyword

### 📝 Generated Content Structure

Each AI-generated article includes:

1. **Meta Information:**
   - SEO-optimized title (55-60 characters)
   - Meta description (150-160 characters)
   - URL-friendly slug
   - Target keywords list

2. **Article Content:**
   - Engaging introduction
   - Well-structured body with headings
   - Key talking points
   - Contextual terms and LSI keywords
   - Strong conclusion
   - FAQ section with Q&A pairs

3. **SEO Enhancements:**
   - Internal link suggestions
   - Inline suggested reads
   - Featured image description
   - Article schema (JSON-LD)
   - FAQ schema (if FAQs included)

4. **Analytics:**
   - SEO score breakdown
   - SEO improvement suggestions
   - Readability analysis

## Setup

### 1. Get Google Gemini API Key

1. Visit [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy your API key (starts with "AIza...")

### 2. Configure API Key in SOFIR

1. Go to **WordPress Admin → SOFIR → SEO** tab
2. Find the "AI Article Generator" section at the top
3. Paste your Google Gemini API key in the field
4. Click "Save API Key"

The AI Article Generator is now ready to use!

## Usage

### Generating an Article

1. **Navigate to SEO Tab:**
   - Go to **SOFIR → SEO** in WordPress admin

2. **Configure Article Settings:**
   - Enter your article title or topic
   - Add target keyword
   - Select article purpose
   - Choose tone
   - Set word count
   - Select point of view
   - Choose readability level
   - Adjust creativity slider
   - Check FAQ/TOC options

3. **Generate:**
   - Click "Generate Article" button
   - Wait 30-60 seconds for AI generation
   - Review the generated content

4. **Review Results:**
   - Check SEO score and suggestions
   - Review content quality
   - Verify keyword placement
   - Check all sections

5. **Save or Publish:**
   - Click "Save as Draft" to save as draft post
   - Click "Publish" to publish immediately
   - You'll be redirected to the post editor

### Using Keyword Research

1. **Switch to Keyword Research Tab:**
   - Click "Keyword Research" tab in AI Generator

2. **Enter Seed Keyword:**
   - Type your main keyword
   - Click "Research Keywords"

3. **Review Results:**
   - Keyword variations with difficulty and volume
   - Long-tail keywords for specific targeting
   - Related and LSI keywords
   - Question-based keywords
   - Trending topics

4. **Use Insights:**
   - Use findings to improve article targeting
   - Select best keywords for your content
   - Identify content opportunities

## Result Tabs

The generated article is organized into tabs:

### 1. Content Tab
- Introduction paragraph
- Full article content with HTML formatting
- Conclusion paragraph
- Copy content button

### 2. Meta Data Tab
- Meta title with character count
- Meta description with character count
- URL slug

### 3. Outline Tab
- Article outline/table of contents
- Heading structure (H2, H3, H4)
- Key talking points
- FAQ section with Q&A

### 4. Keywords Tab
- Target keywords
- Contextual terms and LSI keywords
- Inline suggested reads
- Internal link suggestions with relevance scores
- Featured image suggestion

### 5. SEO Analysis Tab
- SEO improvement suggestions
- Error, warning, info, and success messages
- Actionable recommendations

### 6. Schema Tab
- Article schema (JSON-LD format)
- FAQ schema (if FAQs included)
- Copy schema button for manual use

## Best Practices

### For Best Results:

1. **Be Specific with Title:**
   - Use clear, descriptive titles
   - Include target keyword if possible

2. **Choose Appropriate Tone:**
   - Professional for business content
   - Conversational for blogs
   - Technical for specialized content

3. **Set Realistic Word Count:**
   - 800-1500 words for blog posts
   - 1500-2500 words for guides
   - 2500-5000 words for comprehensive resources

4. **Review and Edit:**
   - Always review AI-generated content
   - Add personal insights
   - Verify facts and statistics
   - Add images and media

5. **Optimize Further:**
   - Follow SEO suggestions
   - Add internal links manually
   - Include images with alt text
   - Format for readability

### SEO Tips:

- ✅ Use primary keyword in title
- ✅ Include keyword in first paragraph
- ✅ Use keyword naturally throughout
- ✅ Add variations and related terms
- ✅ Include FAQ section for featured snippets
- ✅ Use proper heading hierarchy
- ✅ Write compelling meta description
- ✅ Add internal and external links
- ✅ Include images and media
- ✅ Format for easy scanning

## API Endpoints

For developers who want to integrate programmatically:

### Generate Article

```
POST /wp-json/sofir/v1/seo-ai/generate
```

**Parameters:**
- `title` (string, required) - Article title
- `keyword` (string, required) - Target keyword
- `purpose` (string) - Article purpose
- `tone` (string) - Writing tone
- `word_count` (int) - Word count
- `pov` (string) - Point of view
- `readability` (string) - Readability level
- `creativity` (float) - Creativity level (0-1)
- `include_faq` (bool) - Include FAQ section
- `include_toc` (bool) - Include table of contents

**Response:** Complete article object with all generated content

### Research Keywords

```
POST /wp-json/sofir/v1/seo-ai/keywords
```

**Parameters:**
- `keyword` (string, required) - Seed keyword

**Response:** Keyword research data with variations and suggestions

## Troubleshooting

### API Key Issues

**Problem:** "API key is not configured" error
- **Solution:** Verify your API key is saved in SOFIR → SEO settings
- **Check:** API key starts with "AIza..."

**Problem:** "API request failed" error
- **Solution:** Check if your API key is valid
- **Action:** Generate a new key from Google AI Studio

### Generation Issues

**Problem:** Generation takes too long
- **Reason:** Complex requests or high word count
- **Tip:** Try shorter word count (800-1500 words)

**Problem:** Empty or incomplete content
- **Solution:** Try generating again with different settings
- **Check:** API key quota limits

**Problem:** Content not SEO-optimized
- **Solution:** Be more specific with keyword and purpose
- **Tip:** Use the keyword research tool first

## Limitations

- **API Quota:** Free tier has usage limits
- **Generation Time:** 30-60 seconds per article
- **Content Quality:** Always review and edit AI content
- **Fact Checking:** Verify statistics and claims
- **Originality:** May need editing for unique voice

## Support

For issues or questions:
1. Check this documentation first
2. Review Google AI Studio documentation
3. Contact SOFIR support
4. Check WordPress error logs

## Updates

This feature uses Google's Gemini AI API, which is continuously improved by Google. SOFIR will update the integration as new features become available.

---

**Version:** 1.0.0  
**Last Updated:** 2024  
**Requires:** Google Gemini API Key  
**Compatible with:** WordPress 6.3+, PHP 8.0+
