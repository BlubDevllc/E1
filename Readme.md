# EventPlanner WebApp

Een platform waar gebruikers evenementen kunnen aanmaken, beheren en zich inschrijven.

## Project Structuur

```
E1/
├── index.php                 # Hoofdpagina met dashboard
├── php/                      # Backend logica
│   ├── auth/                 # Authenticatie (login, register, logout)
│   ├── config/               # Database configuratie
│   └── events/               # Event management (CRUD)
└── public/                   # Frontend assets
    ├── css/                  # Stylesheets
    ├── includes/             # Herbruikbare PHP includes (header, footer)
    └── js/                   # JavaScript bestanden
```

## Code Conventions

### PHP Naming Conventions
- **Variabelen**: camelCase (`$userName`, `$eventList`)
- **Functies**: camelCase (`getUserById()`, `createEvent()`)
- **Classes**: PascalCase (`EventManager`, `UserAuth`)
- **Constanten**: UPPER_SNAKE_CASE (`DB_HOST`, `MAX_PARTICIPANTS`)
- **Database tabellen**: snake_case (`users`, `event_registrations`)
- **Database kolommen**: snake_case (`user_id`, `created_at`)

### CSS Naming Conventions
- **Classes**: kebab-case (`nav-menu`, `sidebar-header`)
- **IDs**: camelCase (`#sidebarToggle`, `#mainContent`)
- **BEM methodologie** waar mogelijk (`block__element--modifier`)

### JavaScript Naming Conventions
- **Variabelen**: camelCase (`userName`, `isLoggedIn`)
- **Functies**: camelCase (`toggleSidebar()`, `validateForm()`)
- **Constanten**: UPPER_SNAKE_CASE (`API_URL`, `MAX_FILE_SIZE`)

### File Naming
- **PHP bestanden**: snake_case (`user_profile.php`, `event_details.php`)
- **CSS bestanden**: kebab-case (`main-style.css`, `responsive-layout.css`)
- **JS bestanden**: kebab-case (`sidebar-toggle.js`, `form-validation.js`)

## Layout Structuur

### Sidebar Layout
- **Breedte open**: 250px
- **Breedte collapsed**: 60px
- **Animatie duur**: 0.3s ease
- **Mobile breakpoint**: 768px (overlay sidebar)

### Responsive Design
- **Desktop**: Sidebar naast content (flexbox)
- **Tablet**: Kleinere sidebar
- **Mobile**: Overlay sidebar met hamburger menu

## Database Design

### Hoofdtabellen
1. **users** - Gebruikersinformatie
2. **events** - Evenement details
3. **registrations** - Koppeltabel voor inschrijvingen

### Relaties
- Users → Events: 1:N (1 user kan meerdere events maken)
- Users → Registrations: 1:N (1 user kan zich voor meerdere events inschrijven)
- Events → Registrations: 1:N (1 event kan meerdere deelnemers hebben)

## Technische Requirements
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0+
- **Server**: Apache (Laragon)
- **Security**: Prepared Statements voor alle database queries

## Development Guidelines

### Security Best Practices
- Altijd prepared statements gebruiken
- Input validatie en sanitization
- Password hashing met `password_hash()`
- CSRF protection bij formulieren
- XSS prevention met `htmlspecialchars()`

### Code Organization
- Een functie doet één ding
- Maximaal 20 regels per functie
- Duidelijke variabele namen
- Comments bij complexe logica
- Error handling in alle functies

## Git Workflow (indien gebruikt)
- **main**: Productie-klare code
- **develop**: Development branch
- **feature/**: Feature branches (`feature/user-authentication`)
- **fix/**: Bugfix branches (`fix/sidebar-animation`)

## Testing Checklist
- [ ] Cross-browser compatibility (Chrome, Firefox, Safari)
- [ ] Responsive design (Desktop, Tablet, Mobile)
- [ ] Form validation (Client + Server side)
- [ ] Database queries (Test met edge cases)
- [ ] Security testing (SQL injection, XSS)

---
*Laatst bijgewerkt: 26 augustus 2025*