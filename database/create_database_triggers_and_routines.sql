DELIMITER //

CREATE TRIGGER update_stock_after_order
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products
    SET stock = stock - NEW.quantity
    WHERE id = NEW.product_id;
END//

CREATE TRIGGER check_stock_before_cart
BEFORE INSERT ON cart
FOR EACH ROW
BEGIN
    DECLARE available_stock INT;
    SELECT stock INTO available_stock FROM products WHERE id = NEW.product_id;
    IF available_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Niewystarczająca ilość produktu w magazynie';
    END IF;
END//

CREATE FUNCTION calculate_discount(old_price DECIMAL(10,2), new_price DECIMAL(10,2))
RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
    IF old_price IS NULL OR old_price <= new_price THEN
        RETURN 0;
    END IF;
    RETURN ROUND(((old_price - new_price) / old_price) * 100, 2);
END//

DELIMITER ;
