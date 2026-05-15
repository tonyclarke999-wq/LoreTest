---
name: Technical Precision
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#45464d'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#3e49d6'
  on-secondary: '#ffffff'
  secondary-container: '#5964f0'
  on-secondary-container: '#fffbff'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#271901'
  on-tertiary-container: '#98805d'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#e0e0ff'
  secondary-fixed-dim: '#bec2ff'
  on-secondary-fixed: '#00046a'
  on-secondary-fixed-variant: '#242fc0'
  tertiary-fixed: '#fcdeb5'
  tertiary-fixed-dim: '#dec29a'
  on-tertiary-fixed: '#271901'
  on-tertiary-fixed-variant: '#574425'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  display-lg:
    fontFamily: Geist
    fontSize: 36px
    fontWeight: '600'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Geist
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  title-sm:
    fontFamily: Geist
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  body-md:
    fontFamily: Geist
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Geist
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
  label-caps:
    fontFamily: JetBrains Mono
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.05em
  data-mono:
    fontFamily: JetBrains Mono
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  sidebar-width: 260px
  header-height: 64px
  gutter: 24px
  margin-page: 32px
  unit-xs: 4px
  unit-sm: 8px
  unit-md: 16px
---

## Brand & Style
This design system is engineered for high-utility desktop environments where information density and clarity are paramount. It adopts a **Corporate/Modern** aesthetic with a lean toward **Minimalism**, prioritizing functional hierarchy over decorative elements. 

The brand evokes a sense of reliability, systematic order, and technical authority. It is designed for professional operators, engineers, and analysts who require a "heads-up display" experience that remains comfortable during extended periods of use. The interface utilizes a rigorous grid and subtle tonal variations to organize complex data without overwhelming the user.

## Colors
The palette is rooted in a professional Navy and White foundation. 
- **Primary:** A deep, authoritative Navy (#0F172A) used for headers, primary navigation, and high-emphasis text.
- **Secondary:** A technical Blue (#3843D0) for primary actions and interactive states.
- **Neutral:** A range of cool grays starting from a clean white background, moving through Slate-50 (#F8FAFC) for container fills and Slate-200 for borders.
- **Semantic:** Success, Warning, and Error colors are desaturated to maintain the professional tone while remaining distinct.

## Typography
The typography system uses **Geist** for its exceptional clarity and technical "grotesk" feel, ensuring that UI labels and body text remain legible at smaller sizes. **JetBrains Mono** is introduced for metadata, data tables, and code snippets to provide a distinct visual "texture" for raw information versus UI controls.

For the desktop dashboard, we prioritize a high-density type scale. The standard body size is 14px, with 13px used for secondary details. Headings are kept conservative in size to maximize vertical space for data.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy for the core structural elements and a **Fluid Grid** for the content area.
- **Sidebar:** A persistent 260px left-hand navigation anchored to the viewport.
- **Main Canvas:** A 12-column responsive grid with 24px gutters. On ultra-wide monitors, content is capped at 1440px or remains fluid based on the specific module’s data requirements.
- **Density:** The system uses an 8px base unit (4px for tight increments). Dashboard modules should minimize internal padding (typically 16px to 20px) to allow for multi-column data layouts.

## Elevation & Depth
In this technical system, depth is achieved through **Low-contrast outlines** and **Tonal layers** rather than heavy shadows. 
- **Surface Tiers:** The main background is White (#FFFFFF). Secondary containers (like the sidebar or card backgrounds) use a subtle Slate-50 (#F8FAFC).
- **Borders:** Elements are separated by 1px solid borders (#E2E8F0). This "blueprint" style provides clear structural boundaries without adding visual weight.
- **Interaction Depth:** Only floating elements (dropdowns, modals) receive a sharp, low-opacity shadow to indicate they exist on the "Top" z-axis layer.

## Shapes
The shape language is **Soft** (4px / 0.25rem). This slight rounding softens the technical edge of the navy palette without feeling "bubbly" or consumer-grade. Buttons, input fields, and dashboard cards all share this 4px radius. Larger containers, like the main content area panels, may use 6px (rounded-lg) for subtle distinction.

## Components
- **Buttons:** Primary buttons are Solid Navy with white text. Secondary buttons use a Slate-100 fill with Navy text. State changes (hover/active) are indicated by subtle shifts in background value.
- **Data Tables:** High-density rows (32px - 40px height). Header cells use `label-caps` typography. Row dividers are 1px Slate-100. Use monospaced fonts for numerical columns to ensure alignment.
- **Sidebar Items:** Clear, icon-prefixed labels. Active states are indicated by a subtle background tint and a 2px primary-colored left "indicator" bar.
- **Input Fields:** 1px Slate-200 borders that shift to Blue-500 on focus. Labels are placed above the field in `body-sm` bold.
- **Dashboard Cards:** Simple white containers with a 1px border. No shadows. Use "Header-Body-Footer" internal partitioning for complex data modules.
- **Status Pills:** Small, 2px rounded chips with desaturated background tints (e.g., pale emerald for "Stable") to indicate system health without distracting from primary tasks.