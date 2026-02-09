# Font Awesome 6 Migration Guide

This project has been upgraded from Font Awesome 4.7.0 to Font Awesome 6.5.1.

## Key Changes

### 1. Library Update
- **Old**: Local Font Awesome 4.7.0 files
- **New**: Font Awesome 6.5.1 via CDN

### 2. Icon Class Syntax
Font Awesome 6 uses a different class structure:

**Old (FA 4):**
```html
<i class="fa fa-icon-name"></i>
```

**New (FA 6):**
```html
<i class="fa-solid fa-icon-name"></i>  <!-- Solid icons (most common) -->
<i class="fa-regular fa-icon-name"></i> <!-- Regular icons -->
<i class="fa-brands fa-icon-name"></i>  <!-- Brand icons -->
```

### 3. Common Icon Name Changes

| Old Icon (FA 4) | New Icon (FA 6) | Notes |
|----------------|-----------------|-------|
| `fa-pencil-square-o` | `fa-pen-to-square` | Edit icon |
| `fa-trash` | `fa-trash` | Same name, use `fa-solid` |
| `fa-plus` | `fa-plus` | Same name, use `fa-solid` |
| `fa-copy` | `fa-copy` | Same name, use `fa-solid` |
| `fa-expand` | `fa-expand` | Same name, use `fa-solid` |
| `fa-compress` | `fa-compress` | Same name, use `fa-solid` |
| `fa-times` | `fa-xmark` | Close/X icon renamed |
| `fa-hdd-o` | `fa-hard-drive` | Hard drive icon |
| `fa-cog` | `fa-gear` | Settings/gear icon |
| `fa-heartbeat` | `fa-heart-pulse` | Health/heartbeat icon |
| `fa-sign-in` | `fa-right-to-bracket` | Login/sign in icon |
| `fa-microchip` | `fa-microchip` | Same name, use `fa-solid` |
| `fa-memory` | `fa-memory` | Same name, use `fa-solid` |
| `fa-server` | `fa-server` | Same name, use `fa-solid` |
| `fa-plug` | `fa-plug` | Same name, use `fa-solid` |

### 4. Files Already Updated
- `resources/views/layouts/obzorav1.blade.php` - Library CDN link
- `resources/views/overview/default.blade.php` - Dashboard icons
- `resources/views/widgets/server-stats.blade.php` - Server stats icons
- `resources/views/widgets/device-summary-horiz.blade.php` - Device summary icons
- `resources/views/auth/login-form.blade.php` - Login icon

### 5. Remaining Files to Update
There are approximately 338 icon instances across 44 files that still need updating. Common patterns to find and replace:

**Search for:**
- `class="fa fa-` → Replace with `class="fa-solid fa-`
- `fa-pencil-square-o` → `fa-pen-to-square`
- `fa-times` → `fa-xmark`
- `fa-hdd-o` → `fa-hard-drive`
- `fa-cog` → `fa-gear`
- `fa-heartbeat` → `fa-heart-pulse`
- `fa-sign-in` → `fa-right-to-bracket`

### 6. JavaScript Icon Class Manipulation
If JavaScript is adding/removing icon classes, ensure it uses the full class name:
```javascript
// Old
$(this).removeClass('fa-expand').addClass('fa-compress');

// New (if needed, but usually the class names are the same)
$(this).removeClass('fa-expand').addClass('fa-compress');
// Note: Most icon names remain the same, just add fa-solid prefix
```

### 7. Testing Checklist
- [ ] All icons display correctly
- [ ] No broken icon placeholders
- [ ] Icons match the modern Font Awesome 6 style
- [ ] JavaScript icon toggles work (expand/compress, etc.)
- [ ] All pages load without console errors

### 8. Resources
- Font Awesome 6 Icons: https://fontawesome.com/icons
- Migration Guide: https://fontawesome.com/docs/web/setup/upgrade/migrate-from-version-4

