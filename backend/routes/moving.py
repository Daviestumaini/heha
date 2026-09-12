from flask import Blueprint, request, jsonify
from flask_jwt_extended import jwt_required, get_jwt_identity
from extensions import db
from models import MovingRequest

moving_bp = Blueprint("moving", __name__, url_prefix="/api/moving")


@moving_bp.route("/requests", methods=["GET"])
@jwt_required()
def list_requests():
    user_id = get_jwt_identity()
    items = MovingRequest.query.filter_by(user_id=user_id).order_by(MovingRequest.created_at.desc()).all()
    return jsonify([i.to_dict() for i in items]), 200


@moving_bp.route("/requests", methods=["POST"])
@jwt_required()
def create_request():
    user_id = get_jwt_identity()
    data = request.get_json(force=True)
    for field in ("pickup_address", "dropoff_address", "item_list", "preferred_date"):
        if not data.get(field):
            return jsonify({"error": f"{field} is required"}), 400

    mr = MovingRequest(
        user_id=user_id,
        pickup_address=data["pickup_address"],
        dropoff_address=data["dropoff_address"],
        item_list=data["item_list"],
        preferred_date=data["preferred_date"],
    )
    db.session.add(mr)
    db.session.commit()
    return jsonify(mr.to_dict()), 201
