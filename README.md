### Product REST API

A REST API built with pure PHP 8.2+ using an MVC-like structure, PostgresSQL, Clean Code, SOLID principles, design patterns, and PHP Attributes for request validation via a request DTO. Database configuration is read from environment variables using `vlucas/phpdotenv`.

### Features

- CRUD operations for products.
- Request validation using PHP Attributes (`#[Required]`, `#[StringType]`, `#[PositiveNumber]`).
- PostgresSQL database with UUIDs and JSONB for flexible attributes.
- Unit and integration tests with PHPUnit.
- Environment variable configuration via `.env`.

Prerequisites

- PHP 8.2 or higher
- Composer
- PostgresSQL (with a `product_db` database created)
- php8.2-zip
- php8.2-pdo
- php8.2-pgsql

Installation

1. Clone the Repository
   git clone https://github.com/khodemobin/test-case
   cd project

2. Install Dependencies:
   composer install

3. Set Up Environment Variables:
    - Copy `.env.example` to `.env`:
      ```
        cp .env.example .env 
      ```
    - Edit `.env` with your PostgresSQL credentials (all fields are required):
   ```
    DB_HOST=localhost
    DB_NAME=product_db
    DB_USER=your_username
    DB_PASSWORD=your_password
    DB_PORT=5432
    ```  

    - Ensure `.env` is in the project root (`project/.env`).

4. Set Up PostgresSQL:
    - Create the database:
      createdb product_db
    - Run the following SQL to create the `products` table:
      ```sql
      CREATE TABLE products (
      id UUID PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      price NUMERIC(10, 2) NOT NULL,
      category VARCHAR(255),
      attributes JSONB,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
      );
      CREATE INDEX idx_products_category ON products (category);
      CREATE INDEX idx_products_created_at ON products (created_at);
      ```
Running the Application

Start the PHP built-in server:
```shell
  composer run start
```

Running Tests
```shell
  composer run test
```

API Endpoints

All endpoints are accessed via `http://localhost:8000`.

| Method | Endpoint       | Description                      | Request Body (JSON) Example                                                                     |
|--------|----------------|----------------------------------|-------------------------------------------------------------------------------------------------|
| POST   | /products      | Create a product                 | {"name": "Laptop", "price": 999.99, "category": "electronics", "attributes": {"brand": "Dell"}} |
| GET    | /products/{id} | Retrieve a product by ID         | N/A                                                                                             |
| PATCH  | /products/{id} | Update a product (partial)       | {"price": 899.99}                                                                               |
| DELETE | /products/{id} | Delete a product                 | N/A                                                                                             |
| GET    | /products      | List all products (with filters) | N/A (Query: ?category=electronics&price_min=10&price_max=1000)                                  |

Example Requests

- Create a Product:
``` shell 
    curl -X POST http://localhost:8000/products \
  -H "Content-Type: application/json" \
  -d '{"name": "Laptop", "price": 999.99, "category": "electronics", "attributes": {"brand": "Dell"}}'
```
- Get a Product:
  ```shell
    curl http://localhost:8000/products/<uuid>
  ```

- List Products:
  ```shell
    curl "http://localhost:8000/products?category=electronics"
  ```