from datetime import datetime
from extensions import db


class User(db.Model):
    __tablename__ = "users"
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    phone = db.Column(db.String(30))
    password_hash = db.Column(db.String(255), nullable=False)
    role = db.Column(db.String(20), default="customer")  # customer | admin
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def to_dict(self):
        return {
            "id": self.id, "name": self.name, "email": self.email,
            "phone": self.phone, "role": self.role,
        }


class MovingRequest(db.Model):
    __tablename__ = "moving_requests"
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)
    pickup_address = db.Column(db.String(255), nullable=False)
    dropoff_address = db.Column(db.String(255), nullable=False)
    item_list = db.Column(db.Text, nullable=False)
    preferred_date = db.Column(db.String(20), nullable=False)
    status = db.Column(db.String(20), default="pending")  # pending|quoted|confirmed|completed|cancelled
    quote_amount = db.Column(db.Float, nullable=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def to_dict(self):
        return {
            "id": self.id, "pickup_address": self.pickup_address,
            "dropoff_address": self.dropoff_address, "item_list": self.item_list,
            "preferred_date": self.preferred_date, "status": self.status,
            "quote_amount": self.quote_amount,
            "created_at": self.created_at.isoformat(),
        }


class Loan(db.Model):
    __tablename__ = "loans"
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)
    amount_requested = db.Column(db.Float, nullable=False)
    purpose = db.Column(db.String(255), nullable=False)
    term_months = db.Column(db.Integer, nullable=False)
    status = db.Column(db.String(20), default="pending")  # pending|approved|rejected|disbursed|repaid
    interest_rate = db.Column(db.Float, default=12.5)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def to_dict(self):
        return {
            "id": self.id, "amount_requested": self.amount_requested,
            "purpose": self.purpose, "term_months": self.term_months,
            "status": self.status, "interest_rate": self.interest_rate,
            "created_at": self.created_at.isoformat(),
        }


class GuidanceSession(db.Model):
    __tablename__ = "guidance_sessions"
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)
    topic = db.Column(db.String(255), nullable=False)
    preferred_date = db.Column(db.String(20), nullable=False)
    notes = db.Column(db.Text)
    status = db.Column(db.String(20), default="requested")  # requested|scheduled|completed|cancelled
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def to_dict(self):
        return {
            "id": self.id, "topic": self.topic, "preferred_date": self.preferred_date,
            "notes": self.notes, "status": self.status,
            "created_at": self.created_at.isoformat(),
        }


class Product(db.Model):
    __tablename__ = "products"
    id = db.Column(db.Integer, primary_key=True)
    category = db.Column(db.String(30), nullable=False)  # merchandise | phone | case
    name = db.Column(db.String(150), nullable=False)
    description = db.Column(db.Text)
    price = db.Column(db.Float, nullable=False)
    stock = db.Column(db.Integer, default=0)
    image_url = db.Column(db.String(255))

    def to_dict(self):
        return {
            "id": self.id, "category": self.category, "name": self.name,
            "description": self.description, "price": self.price,
            "stock": self.stock, "image_url": self.image_url,
        }


class Order(db.Model):
    __tablename__ = "orders"
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)
    total_amount = db.Column(db.Float, nullable=False)
    status = db.Column(db.String(20), default="pending")  # pending|paid|shipped|delivered|cancelled
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    items = db.relationship("OrderItem", backref="order", cascade="all, delete-orphan")

    def to_dict(self):
        return {
            "id": self.id, "total_amount": self.total_amount, "status": self.status,
            "created_at": self.created_at.isoformat(),
            "items": [i.to_dict() for i in self.items],
        }


class OrderItem(db.Model):
    __tablename__ = "order_items"
    id = db.Column(db.Integer, primary_key=True)
    order_id = db.Column(db.Integer, db.ForeignKey("orders.id"), nullable=False)
    product_id = db.Column(db.Integer, db.ForeignKey("products.id"), nullable=False)
    quantity = db.Column(db.Integer, nullable=False)
    price = db.Column(db.Float, nullable=False)  # price at time of purchase

    def to_dict(self):
        return {
            "id": self.id, "product_id": self.product_id,
            "quantity": self.quantity, "price": self.price,
        }
