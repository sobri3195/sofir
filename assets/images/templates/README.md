# Template Preview Images

This directory contains preview images for SOFIR templates.

## Current Files

- `*.svg` - Placeholder preview images (generated)
- `.gitkeep` - Keep directory in git

## Replacing Placeholders

To replace placeholder images with actual screenshots:

1. Take a screenshot of the template (recommended size: 1200x800px)
2. Optimize the image (use JPEG with 80-90% quality or WebP)
3. Replace the corresponding `.svg` file with your image
4. Update the file extension in `templates/templates.php` if needed

## Naming Convention

Template preview files should match the template slug:
- `startup-launch.svg` → Startup Launch template
- `agency-spotlight.svg` → Agency Spotlight template
- etc.

## Best Practices

- Use consistent dimensions (4:3 aspect ratio recommended)
- Optimize images for web (keep file size under 200KB)
- Consider using WebP format for better compression
- Include key visual elements from the template in the screenshot
