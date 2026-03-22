-- liquibase formatted sql

-- changeset portfolio:1
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY (id)
);

CREATE UNIQUE INDEX IF NOT EXISTS uniq_users_email ON users (email);

-- changeset portfolio:2
CREATE TABLE IF NOT EXISTS curriculum_vitae (
    id VARCHAR(36) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INTEGER NOT NULL,
    file_content BYTEA NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY (id)
);

CREATE INDEX IF NOT EXISTS idx_cv_is_active ON curriculum_vitae (is_active);

-- changeset portfolio:3
CREATE TABLE IF NOT EXISTS projects (
    id VARCHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NULL,
    project_url VARCHAR(500) NULL,
    repo_url VARCHAR(500) NULL,
    technologies TEXT NOT NULL DEFAULT '[]',
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY (id)
);

CREATE UNIQUE INDEX IF NOT EXISTS uniq_projects_slug ON projects (slug);
CREATE INDEX IF NOT EXISTS idx_projects_status ON projects (status);
CREATE INDEX IF NOT EXISTS idx_projects_created_at ON projects (created_at);

-- changeset portfolio:4
CREATE TABLE IF NOT EXISTS page_visits (
    id VARCHAR(36) NOT NULL,
    page VARCHAR(500) NOT NULL DEFAULT '/',
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    referrer VARCHAR(500) NULL,
    visited_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY (id)
);

CREATE INDEX IF NOT EXISTS idx_page_visits_visited_at ON page_visits (visited_at);
CREATE INDEX IF NOT EXISTS idx_page_visits_page ON page_visits (page);
CREATE INDEX IF NOT EXISTS idx_page_visits_ip_address ON page_visits (ip_address);
CREATE INDEX IF NOT EXISTS idx_page_visits_referrer ON page_visits (referrer);
