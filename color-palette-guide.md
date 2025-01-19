# Color Palette Guide

This application uses a consistent color palette system across all views. The colors are organized into three main categories:

## Primary Colors (Blue)
- Main brand color
- Used for primary actions, main navigation, and important UI elements
- Ranges from 50 (lightest) to 900 (darkest)

## Secondary Colors (Purple)
- Used for secondary UI elements, backgrounds, and text
- Provides depth and hierarchy to the interface
- Ranges from 50 (lightest) to 900 (darkest)

## Accent Colors (Red)
- Used for highlights, alerts, and calls to action
- Adds visual interest and draws attention to key elements
- Ranges from 50 (lightest) to 900 (darkest)

## Usage

### Components
We've created standardized components that use these colors consistently:

1. Buttons:
   - `.btn-primary`
   - `.btn-secondary`
   - `.btn-accent`

2. Alerts:
   - `.alert-success`
   - `.alert-warning`
   - `.alert-error`

3. Navigation:
   - `.nav-link`
   - `.nav-link.active`

4. Cards:
   - `.card`
   - `.card-header`

5. Form Elements:
   - `.form-input`
   - `.form-label`

6. Badges:
   - `.badge-primary`
   - `.badge-secondary`
   - `.badge-accent`

### Implementation
The color system is implemented through:
1. Tailwind CSS classes (e.g., `text-primary-600`, `bg-secondary-100`)
2. CSS Variables (e.g., `var(--color-primary-500)`)
3. Component classes that use the color system

To maintain consistency, always use these predefined colors and components rather than creating new color values.