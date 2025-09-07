CREATE SCHEMA IF NOT EXISTS cities;
 
CREATE TABLE IF NOT EXISTS cities.citie (
	 id BIGSERIAL PRIMARY KEY ,
	 name VARCHAR(100),
	 country VARCHAR(100)
);



CREATE SCHEMA IF NOT EXISTS public;
 
CREATE TABLE IF NOT EXISTS public.course (
	 id BIGSERIAL PRIMARY KEY ,
	 name VARCHAR(100) NOT NULL,
	 description VARCHAR(255),
	 credits INT NOT NULL
);



CREATE SCHEMA IF NOT EXISTS public;
 
CREATE TABLE IF NOT EXISTS public.person (
	 id BIGSERIAL PRIMARY KEY ,
	 number_id VARCHAR(255),
	 name VARCHAR(255),
	 lastname VARCHAR(255),
	 email VARCHAR(255),
	 phone VARCHAR(10),
	 address VARCHAR(255)
);



CREATE SCHEMA IF NOT EXISTS public;
 
CREATE TABLE IF NOT EXISTS public.teacher (
	 id BIGSERIAL PRIMARY KEY ,
	 name VARCHAR(100) NOT NULL,
	 description VARCHAR(255),
	 credits INT NOT NULL
);



CREATE TABLE IF NOT EXISTS citie_person (
            id BIGSERIAL PRIMARY KEY,
            citie_id INTEGER NOT NULL REFERENCES citie(id) ON DELETE CASCADE,
            person_id INTEGER NOT NULL REFERENCES person(id) ON DELETE CASCADE,
            UNIQUE(citie_id, person_id)
        );



ALTER TABLE public.teacher ADD COLUMN course_id INTEGER NOT NULL REFERENCES course(id) ON DELETE CASCADE ON UPDATE CASCADE;