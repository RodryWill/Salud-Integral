DROP DATABASE IF EXISTS salud_integral;
CREATE DATABASE salud_integral CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE salud_integral;

-- 1. TABLA USUARIOS
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','lector') DEFAULT 'lector',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABLA RUTINAS (Categorías principales)
CREATE TABLE rutinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    nivel ENUM('Principiante','Intermedio','Avanzado') NOT NULL,
    duracion_minutos INT NOT NULL,
    video_url VARCHAR(255) DEFAULT NULL,
    descripcion TEXT,
    imagen_url VARCHAR(255) DEFAULT NULL
);

-- 3. TABLA EJERCICIOS (El paso a paso de cada rutina)
CREATE TABLE ejercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rutina_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    series VARCHAR(50),
    imagen_url VARCHAR(255),
    video_url VARCHAR(255),
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE
);

-- 4. TABLA PROGRESO
CREATE TABLE progreso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rutina_id INT DEFAULT NULL,
    minutos INT NOT NULL DEFAULT 0,
    fecha DATE NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE SET NULL
);

-- 5. DATOS INICIALES
-- Contraseña: 12345678
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'wmollericona@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdB3qB7.3Oq1rP.Zq7s5S5S5S5S', 'admin');

-- Rutinas Principales (Nivel 1)
INSERT INTO rutinas (titulo, nivel, duracion_minutos, video_url, descripcion) VALUES
('Fisioterapia y Rehabilitación', 'Principiante', 20, 'https://www.w3schools.com/html/mov_bbb.mp4', 'Ejercicios suaves para recuperación de lesiones y movilidad.'),
('Bienestar para Silla de Ruedas', 'Principiante', 15, 'https://www.w3schools.com/html/mov_bbb.mp4', 'Rutina adaptada para personas con movilidad reducida.'),
('Cardio Adaptado en Casa', 'Intermedio', 30, 'https://www.w3schools.com/html/mov_bbb.mp4', 'Circuito cardiovascular de bajo impacto.');

-- Ejercicios (Nivel 2) para Fisioterapia
INSERT INTO ejercicios (rutina_id, nombre, descripcion, series, imagen_url, video_url) VALUES 
(1, 'Elevación de pierna recta', 'Acuéstate boca arriba y eleva una pierna estirada, mantén 3 segundos y baja.', '3 series x 10 reps', 'https://images.unsplash.com/photo-1518611012118-696072aa579a?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4'),
(1, 'Bombeo de tobillo', 'Sentado, mueve los pies hacia arriba y hacia abajo como si pisaras un acelerador.', '2 series x 20 reps', 'https://images.unsplash.com/photo-1599058917212-d750089bc07e?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4'),
(1, 'Estiramiento de isquiotibiales', 'Sentado en el borde de la silla, estira una pierna y lleva el pecho hacia la rodilla.', 'Mantener 30 seg', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4');

-- Ejercicios (Nivel 2) para Silla de Ruedas
INSERT INTO ejercicios (rutina_id, nombre, descripcion, series, imagen_url, video_url) VALUES 
(2, 'Flexiones de pecho en silla', 'Con las manos en los reposabrazos, empuja tu cuerpo hacia arriba usando los brazos.', '3 series x 8 reps', 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4'),
(2, 'Rotación de tronco', 'Con los brazos cruzados, gira suavemente tu torso hacia la derecha y luego a la izquierda.', '2 series x 15 reps', 'https://images.unsplash.com/photo-1552288092-7cd8bcaf0bc1?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4');

-- Ejercicios (Nivel 2) para Cardio Adaptado
INSERT INTO ejercicios (rutina_id, nombre, descripcion, series, imagen_url, video_url) VALUES 
(3, 'Marcha en el lugar', 'Levanta las rodillas alternadamente a un ritmo constante.', '3 min', 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4'),
(3, 'Jab y Cross (Boxeo suave)', 'Lanza golpes al aire de forma controlada, alternando brazos.', '2 min', 'https://images.unsplash.com/photo-1599058917765-a780eda07a3e?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4'),
(3, 'Sentadillas en silla', 'Siéntate y levántate de la silla sin usar las manos.', '3 series x 10 reps', 'https://images.unsplash.com/photo-1574680096145-d05b474e2155?q=80&w=600&auto=format&fit=crop', 'https://www.w3schools.com/html/mov_bbb.mp4');

-- Obtener el ID de tu usuario administrador (normalmente es el 1)
SET @uid = 1;

-- Insertar minutos de entrenamiento de prueba para esta semana
INSERT INTO progreso (usuario_id, minutos, fecha) VALUES 
(@uid, 30, CURDATE()),                -- Hoy (30 min)
(@uid, 45, CURDATE() - INTERVAL 1 DAY), -- Ayer (45 min)
(@uid, 20, CURDATE() - INTERVAL 2 DAY), -- Hace 2 días (20 min)
(@uid, 60, CURDATE() - INTERVAL 3 DAY), -- Hace 3 días (60 min)
(@uid, 15, CURDATE() - INTERVAL 5 DAY); -- Hace 5 días (15 min)

CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    imagen_url VARCHAR(255),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertamos una noticia de prueba
INSERT INTO noticias (titulo, contenido, imagen_url) VALUES 
('¡Bienvenidos a Salud Integral!', 'Estamos muy felices de lanzar nuestra nueva plataforma de rutinas adaptadas. Aquí encontrarás ejercicios para todas las edades.', 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?q=80&w=800&auto=format&fit=crop');
-- Borramos las tablas de biblioteca que no sirven
DROP TABLE IF EXISTS autores;
DROP TABLE IF EXISTS editoriales;
DROP TABLE IF EXISTS categorias;

-- Creamos la tabla de Dietas y Recetas
CREATE TABLE dietas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    tipo ENUM('Desayuno', 'Almuerzo', 'Cena', 'Snack') NOT NULL,
    calorias INT,
    descripcion TEXT,
    imagen_url VARCHAR(255)
);

-- Insertamos dietas de ejemplo
INSERT INTO dietas (titulo, tipo, calorias, descripcion, imagen_url) VALUES 
('Avena con frutas', 'Desayuno', 350, 'Avena cocida con leche vegetal, fresas y plátano. Rica en fibra.', 'https://images.unsplash.com/photo-1517673132405-a56a62b18caf?q=80&w=600&auto=format&fit=crop'),
('Ensalada de pollo y aguacate', 'Almuerzo', 450, 'Pechuga de pollo a la plancha, lechuga, tomate y aguacate con aceite de oliva.', 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=600&auto=format&fit=crop'),
('Salmón al horno con verduras', 'Cena', 500, 'Salmón rico en Omega 3 acompañado de brócoli y zanahorias al vapor.', 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?q=80&w=600&auto=format&fit=crop');

CREATE TABLE testimonios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    comentario TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertamos un par de comentarios de prueba
INSERT INTO testimonios (nombre, comentario) VALUES 
('María López', 'Las rutinas de fisioterapia me ayudaron muchísimo con mi lesión de rodilla. ¡Excelente plataforma!'),
('Carlos Ruiz', 'El modo oscuro y la calculadora de IMC son geniales. Muy fácil de usar.');