create database farmacia;
use farmacia;

create table login(
id int auto_increment primary key,
nome varchar(20) unique not null,
senha varchar(255) not null,
tipoDeConta varchar(20) not null
)CHARACTER SET utf8 COLLATE utf8_bin;

create table cadastroMedicamento(
id_medicamento int auto_increment primary key,
nomeMed varchar(100) not null,
precoMedUni float not null,
quantidadeMedEstoque int not null,
categoriaMed varchar(100) not null,
validadeMed date not null
);

INSERT INTO cadastroMedicamento (nomeMed, precoMedUni, quantidadeMedEstoque, categoriaMed, validadeMed) VALUES
('Paracetamol', 5.50, 100, 'Analgésico', '2025-12-31'),
('Ibuprofeno', 8.75, 50, 'Anti-inflamatório', '2026-01-15'),
('Amoxicilina', 12.00, 75, 'Antibiótico', '2024-11-20'),
('Loratadina', 6.00, 120, 'Antialérgico', '2025-06-10'),
('Omeprazol', 14.30, 60, 'Antiacido', '2024-07-05'),
('Dipirona', 4.50, 150, 'Analgésico', '2025-03-18'),
('Cetirizina', 7.20, 90, 'Antialérgico', '2026-05-25'),
('Metformina', 9.50, 80, 'Antidiabético', '2025-09-12'),
('Atorvastatina', 15.00, 40, 'Hipolipemiante', '2024-10-30'),
('Prednisona', 11.00, 30, 'Corticoide', '2025-02-28');
