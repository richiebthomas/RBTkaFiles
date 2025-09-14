# RBTkaFiles - Simple File Sharing Made Easy

<img width="656" height="101" alt="RBTkaFiles Logo" src="https://github.com/user-attachments/assets/98846aa4-4283-490e-bb2a-a5722cec8ccf" />

## The Problem We're Solving

Sharing files through WhatsApp or OneDrive was too tiring as we always have to login. And sometimes even forget to logout.

Not to mention the unfortunate incident Anish experienced when he received an email from his own account to his inbox. This happened because he forgot to log out, and some menace decided to troll the poor guy in this manner.

We needed something simpler, more secure, and less hassle-free for our team's file sharing needs.

## What is RBTkaFiles?

RBTkaFiles is a lightweight, self-hosted file sharing solution that eliminates the need for complex authentication systems.  
It provides a simple, clean interface for uploading, organizing, and sharing files within your team or organization.

![Files and directories listing](https://github.com/user-attachments/assets/4b2c63f5-1484-4ef7-b51f-24eeda22335f)  
*Files and directories listing*

![Comments](https://github.com/user-attachments/assets/41a29b2f-17b5-47a4-a25e-8092e6a74387)  
*Comments section*

![Printing](https://github.com/user-attachments/assets/3555be5d-8fae-4016-99e3-f54f332697e1)  
*Printing interface*

![Printed document with details](https://github.com/user-attachments/assets/c98d9fb9-72e6-42a9-87ee-2f8059cc53ac)  
*A printed document with name, roll, and lab name*

![Collaborative RTF editing](https://github.com/user-attachments/assets/6635e19e-c1e2-4895-bbfd-b4a73e397f9f)  
*Collaborative RTF editing*


### Key Features

- **No Login Required**: Share files instantly without authentication hassles
- **File Management**: Upload, organize, and manage files with an intuitive interface
- **Collaborative Editor**: Built-in RBTkaWordPad for real-time document collaboration
- **Search & Filter**: Find files quickly with powerful search capabilities
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Print Support**:  Document printing with proper formatting like Name, Roll No, Lab Name

## Tech Stack

### Backend
- **LAMP Stack**:
  - **Linux**: Server operating system
  - **Apache**: Web server
  - **MySQL**: Database management
  - **PHP 8.1+**: Server-side scripting

- **CakePHP 5.0**: Modern PHP framework for rapid development
- **Firepad**: Collaborative text editor integration

### Frontend
- **HTML5/CSS3**: Modern web standards
- **JavaScript (ES6+)**: Client-side functionality
- **Bootstrap 5**: Responsive UI framework
- **jQuery**: DOM manipulation and AJAX


### Database Schema

- **file_items**: Core file metadata and organization
- **users**: User information and preferences
- **directory_notes**: Folder-level documentation
- **visits**: Usage analytics and tracking
- **prints_taken**: Print job logging

## CI/CD Strategy

### Development Workflow
1. **Feature Development**: Create feature branches from `main`
2. **Local Testing**: Run tests and static analysis
3. **Code Review**: Submit pull requests for review
4. **Automated Testing**: CI pipeline runs tests and checks
5. **Deployment**: Merge to main triggers deployment

### Deployment Pipeline
```yaml
# .github/workflows/deploy.yml
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to production
        run: |
          composer install --no-dev
          bin/cake migrations migrate
          bin/cake cache clear_all
```

## Getting Started

### Prerequisites

- **PHP 8.1 or higher**
- **Composer** (latest version)
- **MySQL 5.7+ or MariaDB 10.3+**
- **Apache/Nginx** web server
- **Git** for version control

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/RBTkaFiles.git
   cd RBTkaFiles
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp config/app_local.example.php config/app_local.php
   # Edit config/app_local.php with your database credentials
   ```

4. **Set up the database**
   ```bash
   bin/cake migrations migrate
   ```

5. **Start the development server**
   ```bash
   bin/cake server
   ```

6. **Access the application**
   Open your browser and navigate to `http://localhost:8765`

### Configuration

#### Database Configuration
Edit `config/app_local.php`:
```php
'Datasources' => [
    'default' => [
        'host' => 'localhost',
        'username' => 'your_username',
        'password' => 'your_password',
        'database' => 'rbtkafiles',
    ],
],
```

#### Firebase Configuration
1. Create a Firebase project
2. Enable Realtime Database
3. Add your Firebase config to `config/app_local.php`

The Firebase configuration is stored in `config/app_local.php`:
```php
'Firebase' => [
    'apiKey' => 'your_api_key_here',
    'authDomain' => 'your_project.firebaseapp.com',
    'databaseURL' => 'https://your_project-default-rtdb.region.firebasedatabase.app',
    'projectId' => 'your_project_id',
    'storageBucket' => 'your_project.firebasestorage.app',
    'messagingSenderId' => 'your_sender_id',
    'appId' => 'your_app_id',
],
```

The configuration is automatically loaded and used in the templates.

## How to Contribute

### Setting Up Development Environment

1. **Fork the repository** on GitHub
2. **Clone your fork** locally
   ```bash
   git clone https://github.com/your-username/RBTkaFiles.git
   cd RBTkaFiles
   ```

3. **Install dependencies**
   ```bash
   composer install
   ```

4. **Set up database**
   ```bash
   bin/cake migrations migrate
   ```

5. **Start development server**
   ```bash
   bin/cake server
   ```

### Development Workflow

1. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Write code following PSR-12 standards
   - Add tests for new functionality
   - Update documentation if needed

3. **Run tests and checks**
   ```bash
   # Run tests
   vendor/bin/phpunit
   
   # Check code style
   vendor/bin/phpcs
   
   # Run static analysis
   vendor/bin/phpstan analyse
   ```

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: add new feature description"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a Pull Request**
   - Go to your fork on GitHub
   - Click "New Pull Request"
   - Fill out the PR template
   - Request review from maintainers

### Code Standards

- **PSR-12**: Follow PHP coding standards
- **Meaningful commits**: Use conventional commit messages
- **Documentation**: Update README and code comments
- **Testing**: Write tests for new features
- **Security**: Validate all inputs and sanitize outputs

### Commit Message Format

```
type(scope): description

[optional body]

[optional footer]
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

Examples:
```
feat(file-manager): add drag and drop upload
fix(editor): resolve image sizing issue
docs(readme): update installation instructions
```

## Project Structure

```
RBTkaFiles/
├── config/                 # Configuration files
│   ├── app.php            # Main application config
│   ├── app_local.php      # Local environment config
│   └── Migrations/        # Database migrations
├── src/                   # Source code
│   ├── Controller/        # MVC Controllers
│   ├── Model/            # Data models
│   ├── View/             # View templates
│   └── Service/          # Business logic services
├── templates/            # View templates
│   ├── Files/            # File manager views
│   ├── Pad/              # Editor views
│   └── layout/           # Layout templates
├── webroot/              # Public web files
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── img/              # Images
│   └── uploads/          # User uploaded files
├── tests/                # Test files
├── vendor/               # Composer dependencies
├── composer.json         # PHP dependencies
└── README.md            # This file
```

## API Documentation

### File Operations
- `POST /api/upload` - Upload files
- `GET /api/download/*` - Download files
- `POST /api/delete` - Delete files
- `POST /api/rename` - Rename files
- `POST /api/move` - Move files

### Editor Operations
- `POST /pad/upload-image` - Upload images for editor

### Search Operations
- `GET /api/search/suggestions` - Get search suggestions
- `POST /api/search/suggestions` - Search files and content


## Acknowledgments

- **CakePHP Team** for the excellent framework  
  <p align="center">
    <img src="https://github.com/user-attachments/assets/94509c26-4b4b-4220-b828-de182acff572" alt="CakePHP Logo" width="300"/>
    <br><em>CakePHP</em>
  </p>

- **Firebase** for real-time collaboration features  
  <p align="center">
    <img src="https://github.com/user-attachments/assets/e3e5e889-9afe-4836-ae04-377323369ee8" alt="Firebase Logo" width="300"/>
    <br><em>Firebase</em>
  </p>

- **Bootstrap** for the responsive UI components  
  <p align="center">
    <img src="https://github.com/user-attachments/assets/a73512fe-d6e5-4fc6-ac24-677d37bc0dde" alt="Bootstrap Logo" width="300"/>
    <br><em>Bootstrap</em>
  </p>

- **InfinityFree** for the free hosting services  
  <p align="center">
    <img src="https://github.com/user-attachments/assets/d31fd4ca-860e-4f0e-bff6-e796567431d9" alt="InfinityFree Logo" width="300"/>
    <br><em>InfinityFree</em>
  </p>


**Made with ❤️ by Richie B. Thomas (RBT)**


