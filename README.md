# Admin Tag Manager

This package provides an Admin Tag Manager for managing tags within your application.

## Features

- Create, edit, and delete tag entries
- Organize tags by type or module
- Slug generation for SEO-friendly URLs
- Tag status management (active/inactive)
- User permissions and access control

## Usage

1. **Create**: Add a new tag with name and description.
2. **Read**: View all tags in a paginated list.
3. **Update**: Edit tag information.
4. **Delete**: Remove tags that are no longer needed.

## Example Endpoints

| Method | Endpoint      | Description        |
|--------|---------------|--------------------|
| GET    | `/tags`       | List all tags      |
| POST   | `/tags`       | Create a new tag   |
| GET    | `/tags/{id}`  | Get tag details    |
| PUT    | `/tags/{id}`  | Update a tag       |
| DELETE | `/tags/{id}`  | Delete a tag       |

## Requirements

- PHP 8.2+
- Laravel Framework

## Update `composer.json`

Add the following to your `composer.json` to use the package from a local path:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pavanraj92/admin-tags.git"
    }
]
```

## Installation

```bash
composer require admin/tags:@dev
```

## Usage

1. Publish the configuration and migration files:
    ```bash
    php artisan tag:publish --force

    composer dump-autoload
    
    php artisan migrate
    ```
2. Access the Tag manager from your admin dashboard.

## CRUD Example

```php
// Creating a new FAQ
$tag = new Tag();
$tag->name = 'Featured';
$tag->status = true;
$tag->save();
```

## Customization

You can customize views, routes, and permissions by editing the configuration file.

## License

This package is open-sourced software licensed under the Dotsquares.write code in the readme.md file regarding to the admin/tag manager
