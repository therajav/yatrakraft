# YatraKraft website

Source for the custom WordPress theme behind https://yatrakraft.com.

```
themes/yatrakraft/     Child theme of Travelscape (replaces Travel Journey)
```

## Why a child theme

Everything YatraKraft-specific used to live inside **Travel Journey**, a
third-party child of Travelscape, so any vendor update to Travel Journey would
have wiped it out. `themes/yatrakraft` is our own child of **Travelscape**:

| File | What it does |
|---|---|
| `style.css` | Theme header + Travel Journey's CSS (header pill, nav, mobile header, footer), ported verbatim |
| `functions.php` | Travel Journey's PHP, ported: style/font enqueue (Outfit + Inter, dequeues Travelscape's fonts), colour/font defaults via `travelscape_style`, desktop/mobile header layout (now with the profile icon) |
| `theme.json` | Travel Journey's font families/sizes (Travelscape skips its runtime theme.json injection for child themes) |
| `inc/customizer.php` | Optional header button (Customizer → Header → Header Button) |
| `inc/theme-mods.php` | Carries Travel Journey's Customizer settings over (see below) |
| `inc/yatrakraft.php` | WTE page detection, template routing, asset enqueue, body classes, profile icon, hides "My Account" menu item, `yk_guide_card()` |
| `home.php`, `category.php` → `yk/guides-index.php` | Travel Guides index |
| `yk-checkout.php`, `yk-thankyou.php`, `yk/trust-strip.php` | WP Travel Engine checkout / thank-you wrappers |
| `assets/css/yk-global.css` | Header profile icon + alignment (site-wide) |
| `assets/css/yk-redesign.css` | Guides, checkout, thank-you (only on those pages) |

Differences from the Travel Journey copy: functions renamed `travel_journey_*` →
`yatrakraft_*`, style handle `travel-journey-style` → `yatrakraft-style`, text
domain `yatrakraft`, header-button theme mods renamed
`travel_journey_header_button_*` → `yatrakraft_header_button_*`, the duplicated
trust strip moved into one partial, and Travel Journey's dead
`travelscape_demo_importer_checked()` (never hooked) dropped.

### Customizer settings (theme mods)

WordPress stores Customizer settings per theme, so a new theme would start with
no logo, header image, social links, colours or copyright text.
`inc/theme-mods.php` handles this:

- **Before activation** (preview) it reads Travel Journey's settings on the fly,
  so the preview matches the live site. Nothing is written.
- **On activation** it copies them once into `theme_mods_yatrakraft` and records
  `yatrakraft_theme_mods_migrated`. Menu locations and widgets are left to core,
  which remaps them on theme switch (same Travelscape locations/sidebars).

Additional CSS (Customizer) is also per theme, but Travel Journey has none
(`custom_css_post_id = -1`).

## Deploying

The site is managed through the WPVibe connector (draft → preview → publish).
Never publish without the owner's go-ahead, and take a Hostinger backup first.

1. Create the draft theme and upload every file under `themes/yatrakraft/`.
   Delete any scaffold templates the tool adds (`index.php`, `header.php`,
   `footer.php`): the parent's templates must be used.
2. Screenshot the preview at **390 / 768 / 1440 px** and compare with live:
   home, /trips/, a single trip, /blog/, a guide post, contact, checkout with a
   trip in cart, thank-you after a test "Book only" booking, /my-account/.
3. After activation check Appearance → Menus (locations `menu-1`, `top-menu`)
   and Appearance → Widgets (footer one–four, WTE sidebar).
4. Keep `travel-journey` installed (not active) as a rollback until the new
   theme has been live for a while.

## Gotchas

- A WPCode snippet defines `--yk-*` variables and `.yk-card`, `.yk-cta`,
  `.yk-lead`, `.yk-steps`, `.yk-step`, `.yk-form`, `.yk-faq`, `.yk-plan`,
  `.yk-facts`, `.yk-answer`, `.yk-btns`, `.yk-map`, `.yk-cards`,
  `.yk-newsletter`. Theme CSS uses `--ykr-*` and `.ykg-*` instead; never reuse
  those names.
- Never set `display` on `.site-header .header-desktop` (the parent hides it on
  mobile; overriding it caused a double header).
- The desktop nav has `padding: 10px 30px 0 5px`, so the profile icon uses
  `margin-top: 10px` to line up with the menu text.
- The mobile hamburger is an inline SVG in an `<a>`, so it needs `line-height: 0`
  plus a fixed-size flex box to centre with the profile icon.
- Travelscape's `style.css` is 74 KB; WPVibe will not read files over 64 KB.
- WPCode also injects the logo size (56 / 48 / 44 px heights).
