CREATE TABLE IF NOT EXISTS products
(
    id         UUID PRIMARY KEY,
    name       VARCHAR(255)   NOT NULL,
    price      NUMERIC(10, 2) NOT NULL,
    category   VARCHAR(255),
    attributes JSONB,
    created_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_products_category ON products (category);
CREATE INDEX idx_products_created_at ON products (created_at);