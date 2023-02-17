CREATE TABLE urls (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name varchar(255) NOT NULL,
    created_at timestamp,
    UNIQUE (name)
);

CREATE TABLE url_checks (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    url_id bigint REFERENCES urls (id),
    status_code integer,
    h1 varchar(255),
    title varchar(255),
    description varchar(255),
    created_at timestamp
);