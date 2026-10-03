# RTH Modern Official Design System (Google Stitch Standard)

This specification defines the core design tokens, color palettes, typography hierarchy, elevation surfaces, and component architectures for RTH Modern. All CSS and HTML templates must adhere strictly to these machine-readable and human-editable tokens to ensure pristine visual consistency and a highly professional enterprise user experience.

---

## 1. Brand Tokens & Color System

The color system moves away from heavy gaming gradients to a sophisticated Slate/Navy professional palette paired with vibrant Google Material You accent colors.

### 1.1 Core Brand Palette
```css
:root {
  /* Surface & Background Hierarchy */
  --bg-dark: #0b0f19;          /* Deep obsidian canvas background */
  --surface: #131b2e;          /* Primary card and container surface */
  --surface-elevated: #1e293b; /* Hovered cards, dropdowns, popups */
  --surface-active: #2563eb15; /* Active tab or selected row highlight */

  /* Borders & Glass Dividers */
  --glass-border: rgba(255, 255, 255, 0.08);
  --glass-border-hover: rgba(255, 255, 255, 0.18);
  --border-subtle: #334155;

  /* Primary Accent (Google Blue) */
  --primary: #2563eb;          /* Primary interactive buttons, focus rings */
  --primary-hover: #1d4ed8;    /* Button hover state */
  --primary-light: #60a5fa;    /* Links and subtle accents */
  --primary-glow: rgba(37, 99, 235, 0.25);

  /* Typography */
  --text-main: #f8fafc;        /* High-contrast primary headings and text */
  --text-muted: #94a3b8;       /* Secondary labels, metadata, table headers */
  --text-disabled: #64748b;    /* Inactive or disabled text */
}
```

### 1.2 Semantic & Status Palette
```css
:root {
  /* Success / Pass */
  --success: #10b981;          /* Pass status, green indicators */
  --success-bg: rgba(16, 185, 129, 0.15);
  --success-border: rgba(16, 185, 129, 0.3);

  /* Warning / WIP / Confirmed */
  --warning: #f59e0b;          /* WIP, yellow/amber indicators */
  --warning-bg: rgba(245, 158, 11, 0.15);
  --warning-border: rgba(245, 158, 11, 0.3);

  /* Danger / Fail / Critical / Immediate */
  --danger: #f43f5e;           /* Fail status, red/rose indicators */
  --danger-bg: rgba(244, 63, 94, 0.15);
  --danger-border: rgba(244, 63, 94, 0.3);

  /* Info / Assigned / Normal */
  --info: #3b82f6;             /* Assigned or informational badges */
  --info-bg: rgba(59, 130, 246, 0.15);
  --info-border: rgba(59, 130, 246, 0.3);

  /* Purple / Relationships */
  --purple: #a855f7;           /* Traceability links, matrix headers */
  --purple-bg: rgba(168, 85, 247, 0.15);
  --purple-border: rgba(168, 85, 247, 0.3);
}
```

---

## 2. Typography Hierarchy

Utilizing modern Google Fonts (`Inter`, `Roboto`, `Outfit`) to achieve perfect legibility, crisp font weights, and professional spacing.

```css
:root {
  --font-sans: 'Inter', 'Roboto', 'Outfit', system-ui, -apple-system, sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', monospace;

  /* Font Sizes */
  --text-xs: 0.75rem;     /* 12px - Badges, tiny metadata */
  --text-sm: 0.875rem;    /* 14px - Form labels, table cells, secondary text */
  --text-base: 0.95rem;   /* 15.2px - Body copy, standard inputs */
  --text-lg: 1.125rem;    /* 18px - Subheadings, card titles */
  --text-xl: 1.35rem;     /* 21.6px - Modal headers, section titles */
  --text-2xl: 1.75rem;    /* 28px - Page headers */
  --text-3xl: 2.25rem;    /* 36px - Dashboard metric totals */

  /* Line Heights & Weights */
  --line-height-tight: 1.2;
  --line-height-normal: 1.5;
  --weight-regular: 400;
  --weight-medium: 500;
  --weight-semibold: 600;
  --weight-bold: 700;
}
```

---

## 3. Surface & Elevation System

Surfaces use precise backdrop blur values combined with multi-layered box shadows to create realistic visual depth and crisp separation.

```css
:root {
  --blur-md: blur(12px);
  --blur-lg: blur(16px);
  --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.2);
  --shadow-md: 0 4px 12px -2px rgba(0, 0, 0, 0.3);
  --shadow-lg: 0 12px 28px -6px rgba(0, 0, 0, 0.4);
  --shadow-focus: 0 0 0 3px rgba(37, 99, 235, 0.35);
  --radius-sm: 0.375rem;
  --radius-md: 0.625rem;
  --radius-lg: 1rem;
  --radius-xl: 1.25rem;
}
```

---

## 4. Component Architecture Specification

### 4.1 Buttons & Quick Actions
- **Primary Button (`.btn-primary`)**: Solid `--primary` background with `--weight-semibold`, crisp padding (`0.65rem 1.25rem`), `--radius-md`, and smooth transform on hover (`translateY(-1px)`). Focus state must apply `--shadow-focus`.
- **Secondary Button (`.btn-secondary`)**: Background `--surface-elevated`, text `--text-main`, border `--glass-border`. Hover transitions to `--glass-border-hover`.

### 4.2 Form Inputs & Controls
- **Standard Input (`.form-control`)**: Background `--surface`, text `--text-main`, border `--glass-border`, padding (`0.65rem 1rem`), `--radius-md`.
- **Focus State**: Background transitions to slightly lighter `--surface-elevated`, border transitions to `--primary`, and applies `--shadow-focus`.

### 4.3 Data Tables
- **Table Container (`.table-container`)**: Background `--surface`, border `--glass-border`, `--radius-lg`, with `overflow: hidden`.
- **Headers (`th`)**: Background `rgba(15, 23, 42, 0.6)`, padding (`1rem 1.25rem`), font `--text-sm`, text `--text-muted`, `--weight-semibold`, uppercase with `0.05em` letter spacing.
- **Rows (`tr`)**: Border bottom `--glass-border`. Hover applies `--surface-active` highlight.

### 4.4 Status Badges (`.badge`)
- **Structure**: Inline flex, padding (`0.25rem 0.75rem`), font `--text-xs`, `--weight-semibold`, `--radius-xl`.
- **Variants**:
  - `.badge-green`: `--success-bg` background, `--success` text, `--success-border` border.
  - `.badge-yellow`: `--warning-bg` background, `--warning` text, `--warning-border` border.
  - `.badge-red`: `--danger-bg` background, `--danger` text, `--danger-border` border.
  - `.badge-blue`: `--info-bg` background, `--info` text, `--info-border` border.
  - `.badge-purple`: `--purple-bg` background, `--purple` text, `--purple-border` border.
