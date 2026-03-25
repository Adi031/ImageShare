# ImageShare Project Reboot

The previous attempt at injecting Bootstrap into the global layout inherently conflicted with the user's custom CSS grid ([homePage.css](file:///c:/xampp/htdocs/ImageShare/homePage.css)), causing catastrophic UI overlaps when cached. The user has ordered a complete restart to "do everything properly."

## The "Perfect" Approach
We will rebuild the PHP backend layer directly on top of the user's **original** HTML structures, completely abandoning Bootstrap for global layouts to ensure 100% compatibility with their existing CSS and Javascript previewer.

## Proposed Changes

### 1. Global Assets Reboot
- **Revert [homePage.css](file:///c:/xampp/htdocs/ImageShare/homePage.css)**: Restore the user's exact dark-mountain CSS theme, removing all traces of the glassmorphic overhaul.
- **Revert [header.php](file:///c:/xampp/htdocs/ImageShare/includes/header.php)**: Strip out Bootstrap. Use the user's original `<header>` and `.navbar` HTML snippet exactly as it was.

### 2. Core Pages ([index.php](file:///c:/xampp/htdocs/ImageShare/index.php), [about.php](file:///c:/xampp/htdocs/ImageShare/about/about.php))
- **[index.php](file:///c:/xampp/htdocs/ImageShare/index.php)**: Restore the original `.container` and flexbox calc() grid. Implement PHP loops `while($row = mysqli_fetch_assoc())` directly replacing the static image tags. Leave [homePage.js](file:///c:/xampp/htdocs/ImageShare/homePage.js) completely unmodified so the original previewer works flawlessly.

### 3. Authentication (`auth/`)
- **[login.php](file:///c:/xampp/htdocs/ImageShare/login.php) & [signup.php](file:///c:/xampp/htdocs/ImageShare/auth/signup.php)**: Use the user's native [login.css](file:///c:/xampp/htdocs/ImageShare/auth/login.css) and [signup.css](file:///c:/xampp/htdocs/ImageShare/auth/signup.css) files. Implement secure PHP `$_SESSION` and `password_hash()` logic without modifying the HTML structure heavily. No Bootstrap wrappers. Include a strict `username` field.

### 4. Admin and Upload ([admin.php](file:///c:/xampp/htdocs/ImageShare/admin.php), [upload.php](file:///c:/xampp/htdocs/ImageShare/upload.php))
- Write bespoke, simple CSS inside these files that visually matches the dark mountain theme but relies on standard HTML forms. No Bootstrap grid hacking. 

## Verification
- Test all pages to ensure no CSS classes overlap.
- Ensure the original [homePage.js](file:///c:/xampp/htdocs/ImageShare/homePage.js) lightbox works perfectly when clicking a dynamically loaded image.
- Validate PHP sessions securely block unauthenticated users from Upload/Admin.
*   **Search and Content Filtering**:
    *   Live search bar to find images by title or tags using **AJAX** (no page reload).
    *   Filter images by Categories (e.g., Nature, Technology, Art, Memes).
*   **Image Interactions**:

### Advanced/Bonus Features (To stand out in 6th Sem)
These features add value without excessive complexity:
*   **Save/Pin to Boards**: Allow users to save other people's images into their own personal "Boards" or collections.
*   **Infinite Scrolling**: As the user scrolls down the homepage, load more images automatically via **AJAX**.
*   **Admin Moderation Dashboard**: A simple "Admin only" page to delete any image or category.
*   **User Profiles**: Public profiles showing a user's uploaded images and saved boards.
*   **API Usage**: Fetch random daily inspirational images from a public API (like Unsplash API) using PHP/cURL.

---

## 2. Technology Mapping

How each required technology will be utilized in this project:

*   **HTML5 / CSS3**: We will utilize your existing [homePage.css](file:///c:/xampp/htdocs/ImageShare/homePage.css) and navbar structure, enhancing it with Bootstrap 5 components.
*   **Bootstrap 5**: For rapid responsive layout, navigation bars, and modal popups.
*   **JavaScript & jQuery**: Enhancing your existing [homePage.js](file:///c:/xampp/htdocs/ImageShare/homePage.js) lightbox to fetch dynamic data (like comments and tags) via AJAX.
*   **AJAX**: 
    *   To submit likes and comments dynamically without refreshing the page.
    *   To fetch live search results.
*   **PHP**: Upgrading your existing [db.php](file:///c:/xampp/htdocs/ImageShare/db.php) to use **PDO** for better security and modularity.
*   **MySQL**: Relational database (Schema already defined in [database.sql](file:///c:/xampp/htdocs/ImageShare/database.sql)).
*   **Sessions & Cookies**: For user login states and "Remember Me" features.

---

## 3. Proposed Database Schema

The database will be highly relational, showcasing good normalization practices.

### Tables:
1.  **`users`**: `user_id` (PK), `username`, `email`, `password_hash`, `profile_pic`, `role` (ENUM: 'user', 'admin'), `created_at`
2.  **`categories`**: `category_id` (PK), `name`
3.  **`images`**: `image_id` (PK), `user_id` (FK), `title`, `description`, `file_path`, `category_id` (FK), `created_at`
4.  **`likes`**: `user_id` (FK), `image_id` (FK) *(Composite Primary Key)*
5.  **`comments`**: `comment_id` (PK), `image_id` (FK), `user_id` (FK), `comment_text`, `created_at`
6.  **`boards`** *(Bonus)*: `board_id` (PK), `user_id` (FK), `name`, `description`
7.  **`board_images`** *(Bonus)*: `board_id` (FK), `image_id` (FK)

---

## 4. Development Phases (Workflow for 4 People)

To divide the work among your group efficiently:

### Phase 1: Planning & Setup
*   Set up XAMPP/WAMP environments.
*   Initialize the MySQL database and create the tables.
*   Design the Core UI (HTML/CSS/Bootstrap) - Homepage, Login/Register pages, Profile page.

### Phase 2: Backend Core (Authentication & Uploads)
*   Implement PHP Registration and Login logic (password hashing, sessions).
*   Implement the Image Upload system (handling file storage on the server and saving metadata in the database).

### Phase 3: Feed & Discovery
*   Fetch images from the database and display them on the homepage in a responsive grid.
*   Implement the category filtering system.

### Phase 4: Interactivity & AJAX
*   Implement AJAX Live Search.
*   Implement AJAX Liking and Commenting systems.
*   Implement Infinite Scrolling.

### Phase 5: Polish & Security
*   Ensure all PHP queries use **Prepared Statements** to prevent SQL Injection.
*   Sanitize all user inputs to prevent XSS attacks.
*   Final UI adjustments, animations, and cross-browser testing.
