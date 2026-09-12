from flask import Blueprint, request, jsonify
from flask_jwt_extended import jwt_required, get_jwt_identity
from extensions import db
from models import Product, Order, OrderItem

shop_bp = Blueprint("shop", __name__, url_prefix="/api")


@shop_bp.route("/products", methods=["GET"])
def list_products():
    category = request.args.get("category")  # merchandise | phone | case
    query = Product.query
    if category:
        query = query.filter_by(category=category)
    products = query.all()
    return jsonify([p.to_dict() for p in products]), 200


@shop_bp.route("/orders", methods=["POST"])
@jwt_required()
def create_order():
    """Body: { "items": [ {"product_id": 1, "quantity": 2}, ... ] }"""
    user_id = get_jwt_identity()
    data = request.get_json(force=True)
    cart_items = data.get("items", [])
    if not cart_items:
        return jsonify({"error": "Cart is empty"}), 400

    total = 0.0
    order_items = []
    for entry in cart_items:
        product = Product.query.get(entry.get("product_id"))
        qty = int(entry.get("quantity", 0))
        if not product or qty <= 0:
            return jsonify({"error": "Invalid item in cart"}), 400
        if product.stock < qty:
            return jsonify({"error": f"Not enough stock for {product.name}"}), 400
        total += product.price * qty
        order_items.append((product, qty))

    order = Order(user_id=user_id, total_amount=total)
    db.session.add(order)
    db.session.flush()  # get order.id before commit

    for product, qty in order_items:
        db.session.add(OrderItem(order_id=order.id, product_id=product.id,
                                  quantity=qty, price=product.price))
        product.stock -= qty

    db.session.commit()
    return jsonify(order.to_dict()), 201


@shop_bp.route("/orders", methods=["GET"])
@jwt_required()
def list_orders():
    user_id = get_jwt_identity()
    orders = Order.query.filter_by(user_id=user_id).order_by(Order.created_at.desc()).all()
    return jsonify([o.to_dict() for o in orders]), 200
