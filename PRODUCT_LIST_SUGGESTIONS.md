# Product List Suggestions Feature

## Overview

The **Product List Suggestions** feature enhances the SEO AI Article Generator by providing AI-powered product recommendations from Google SERP and allowing manual product link management. This feature is available for Product Roundup, Product Review, and Comparison article types.

## Features

### 1. AI-Powered Product Suggestions
- Get intelligent product recommendations using Google Gemini AI
- AI analyzes your search query and suggests 5-10 relevant products
- Each suggestion includes:
  - Product name
  - Product URL (placeholder or suggested link)
  - Brief description of key features

### 2. Manual Product Management
- Add products manually with custom details
- Edit product information inline:
  - Product Name
  - Product URL
  - Product Description
- Remove products from the list
- Full control over product data

### 3. Visual Product List Manager
- Beautiful table interface for managing products
- Real-time editing with instant updates
- Product counter showing total number of products
- Responsive design for all screen sizes

## How to Use

### Getting AI Suggestions

1. Navigate to **SOFIR → SEO** in WordPress admin
2. In the AI Article Generator, select one of these article types:
   - **Product Roundup**
   - **Product Review**
   - **Comparison Post**
3. Scroll down to the **📦 Product List Manager** section
4. Enter your search query (e.g., "best wireless headphones 2024")
5. Click **🤖 Get AI Suggestions**
6. AI will suggest 5-10 relevant products with names, URLs, and descriptions
7. Edit any product details directly in the table

### Adding Products Manually

1. In the **Product List Manager** section
2. Click **➕ Add Product**
3. A new row will appear in the table
4. Fill in:
   - **Product Name**: The name of the product
   - **Product URL**: Link to the product page (e.g., Amazon, manufacturer website)
   - **Description**: Brief description of key features
5. Repeat for additional products

### Editing Product Information

1. Click on any field in the product table
2. Edit the text directly
3. Changes are saved automatically
4. Continue editing other fields as needed

### Removing Products

1. Click the **Remove** button next to any product
2. Product will be removed from the list immediately
3. Product counter updates automatically

### Generating Article with Product List

1. After adding/editing products, fill in other article details:
   - Article Title
   - Target Keyword
   - Tone, Word Count, etc.
2. Click **Generate Article**
3. AI will use your product list to create:
   - Product comparison tables
   - Individual product sections
   - Pros and cons for each product
   - Purchase links and CTAs
   - SEO-optimized product descriptions

## Use Cases

### Product Roundup Article
- Search: "best smartphones 2024"
- Get AI suggestions for top 10 smartphones
- Edit URLs to include affiliate links
- Generate comprehensive roundup with comparison table

### Product Review Article
- Search: "iPhone 15 Pro review"
- Get AI suggestion for iPhone 15 Pro
- Add official product link
- Generate in-depth review with specs and testing

### Comparison Post
- Search: "iPhone vs Samsung Galaxy"
- Get AI suggestions for both products
- Add both product URLs
- Generate head-to-head comparison

## Technical Details

### API Endpoints

#### Get Product Suggestions
```
POST /wp-json/sofir/v1/seo-ai/product-suggestions
```

**Parameters:**
- `query` (required): Search query for products

**Response:**
```json
{
  "products": [
    {
      "name": "Product Name",
      "url": "https://example.com/product",
      "description": "Brief description of key features"
    }
  ]
}
```

### AJAX Actions

#### Get Product Suggestions
```javascript
wp_ajax_sofir_get_product_suggestions
```

**Data:**
- `action`: "sofir_get_product_suggestions"
- `nonce`: AJAX nonce for security
- `query`: Search query string

### Data Structure

Products are stored as JSON array:
```json
[
  {
    "name": "Product Name",
    "url": "https://example.com/product",
    "description": "Product description"
  }
]
```

This data is:
- Sent to the article generation API
- Used to build product-specific prompts
- Included in generated articles with proper formatting

## Integration with Article Types

### Product Roundup
- All products in list are included in article
- Comparison table shows all products side-by-side
- Each product gets dedicated section
- Best for 5-10 products

### Product Review
- Uses first product in list
- Focuses on single product in-depth
- Includes product URL for purchase CTA
- Best for detailed analysis

### Comparison Post
- Uses 2-3 products from list
- Head-to-head comparison
- Winner declarations per category
- Best for direct comparisons

## Best Practices

1. **Search Queries**: Be specific with your search queries
   - ✅ "best wireless headphones under $200 2024"
   - ❌ "headphones"

2. **Product URLs**: Use clean, traceable links
   - Add affiliate parameters if needed
   - Use shortened URLs for better tracking
   - Test links before generating article

3. **Descriptions**: Keep descriptions concise
   - Highlight 1-2 key features
   - Focus on unique selling points
   - Use power words

4. **Product Count**:
   - Product Roundup: 5-10 products ideal
   - Product Review: 1 product
   - Comparison: 2-3 products maximum

5. **Product Order**: Products appear in article in list order
   - Put best product first for rankings
   - Consider user journey
   - Group similar products

## SEO Benefits

1. **Structured Data**: Product links enable rich snippets
2. **User Intent**: Matching products to search intent
3. **Internal Linking**: Connect products to category pages
4. **Affiliate Revenue**: Direct product links increase conversions
5. **Content Quality**: Real product data improves authenticity

## Tips & Tricks

### For Affiliate Marketers
- Add tracking parameters to URLs
- Test click-through rates
- Update products regularly
- Monitor product availability

### For Reviewers
- Link to official product pages
- Include comparison products
- Add price information in descriptions
- Update specifications as products evolve

### For E-commerce Sites
- Link to your own product pages
- Include stock status in descriptions
- Add special offers in descriptions
- Connect to inventory system

## Troubleshooting

### AI Suggestions Not Working
- Check if Google Gemini API key is configured
- Verify API key has sufficient quota
- Try more specific search queries
- Check network connectivity

### Products Not Appearing in Article
- Ensure product list has data before generating
- Check that article type is set correctly
- Verify product URLs are valid
- Review console for JavaScript errors

### Formatting Issues
- Use plain text in product fields
- Avoid special characters in URLs
- Keep descriptions under 200 characters
- Test product links before publishing

## Future Enhancements

Coming soon:
- Direct Google Shopping integration
- Amazon Product API integration
- Price tracking and updates
- Product image suggestions
- Review score aggregation
- Stock availability checking

## Support

For issues or questions:
1. Check this documentation
2. Review console logs for errors
3. Test with simpler queries
4. Contact SOFIR support

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**Compatibility**: WordPress 5.8+, PHP 8.0+
