CREATE USER 'admin'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON quiz_mania.* TO 'admin'@'%';
FLUSH PRIVILEGES;

