from flask import Blueprint, request, jsonify
from flask_jwt_extended import create_access_token
from extensions import db, bcrypt
from models import User

auth_bp = Blueprint("auth", __name__, url_prefix="/api/auth")


@auth_bp.route("/register", methods=["POST"])
def register():
    data = request.get_json(force=True)
    for field in ("name", "email", "password"):
        if not data.get(field):
            return jsonify({"error": f"{field} is required"}), 400

    if User.query.filter_by(email=data["email"]).first():
        return jsonify({"error": "Email already registered"}), 409

    pw_hash = bcrypt.generate_password_hash(data["password"]).decode("utf-8")
    user = User(
        name=data["name"], email=data["email"],
        phone=data.get("phone"), password_hash=pw_hash,
    )
    db.session.add(user)
    db.session.commit()

    token = create_access_token(identity=user.id)
    return jsonify({"token": token, "user": user.to_dict()}), 201


@auth_bp.route("/login", methods=["POST"])
def login():
    data = request.get_json(force=True)
    user = User.query.filter_by(email=data.get("email")).first()
    if not user or not bcrypt.check_password_hash(user.password_hash, data.get("password", "")):
        return jsonify({"error": "Invalid email or password"}), 401

    token = create_access_token(identity=user.id)
    return jsonify({"token": token, "user": user.to_dict()}), 200
