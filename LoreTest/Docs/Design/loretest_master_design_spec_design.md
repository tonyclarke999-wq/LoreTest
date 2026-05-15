# LoreTest Master Design Specification

This document provides a consolidated reference for the LoreTest design language, encompassing both the high-density Desktop interface and the modern Mobile suite.

## 1. Design Systems

We have established two distinct but related design systems to optimize for different hardware and user contexts.

### A. Technical Precision (Desktop)
Designed for high-density QA workflows, emphasizing data visibility and professional precision.
- **Primary Font**: Geist (Technical sans-serif)
- **Base Color Palette**:
  - `surface`: #f7f9fb
  - `primary`: #0f172a (Deep Navy)
  - `secondary`: #3b82f6 (Accent Blue)
- **Key Tokens**:
  - `roundness`: 4px (Subtle, professional)
  - `spacing`: Tighter, optimized for data tables and multi-column layouts.

### B. LoreTest Technical (Mobile)
A modern, mobile-first system designed for legibility and ease of interaction on smaller screens.
- **Primary Font**: Hanken Grotesk
- **Base Color Palette**:
  - `surface`: #f8f9ff (Cooler white)
  - `primary`: #0056d2 (High-contrast Blue)
- **Key Tokens**:
  - `roundness`: 8px - 12px (More approachable, modern feel)
  - `spacing`: Generous gutters and card-based grouping.

---

## 2. Shared Components (Architecture)

These components form the shell of the application across both platforms.

### Navigation Shells
- **SideNavBar (Desktop)**: Persistent left-side navigation with vertical tabs (Dashboard, Projects, Test Suites, Execution, Settings). Includes brand logo and user profile summary at the bottom.
- **TopNavBar (Desktop)**: Integrated search, global "Create" action, and system notifications.
- **BottomNavBar (Mobile)**: Primary navigation for mobile, providing thumb-friendly access to Home, Projects, Execute, and Activity.
- **TopAppBar (Mobile)**: Contextual header with menu triggers and user profile access.

---

## 3. UI Patterns & Layouts

### Data Representation
- **Desktop**: Prioritizes **Data Tables** for scanning large sets of projects or suites. Includes inline status badges (Ready, Drafting, Unstable).
- **Mobile**: Utilizes **Card-based Layouts** to group related information (Project Reference, Title, Team Avatars) into digestible units.

### Manual Execution Flow
Designed to maintain tester focus:
- **Split-screen (Desktop)**: Action description on the left, results and history on the right.
- **Linear Step View (Mobile)**: Prominent step description and large "Pass/Fail" buttons for one-handed operation.

---

## 4. Asset Integration
All iconography uses standard Google Material symbols for clarity. Brand assets and uploaded screenshots are integrated via secure DataStore placeholders for consistency across environments.

---

## 5. Development Notes
- **Framework**: Tailwind CSS (Utility-first styling).
- **Icons**: Material Design Icons.
- **Responsive Strategy**: Not a single fluid layout, but separate specialized views for Mobile and Desktop to ensure optimal user experience in each context.
