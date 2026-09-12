from flask import Blueprint, request, jsonify
from flask_jwt_extended import jwt_required, get_jwt_identity
from extensions import db
from models import GuidanceSession

guidance_bp = Blueprint("guidance", __name__, url_prefix="/api/guidance")


@guidance_bp.route("", methods=["GET"])
@jwt_required()
def list_sessions():
    user_id = get_jwt_identity()
    items = GuidanceSession.query.filter_by(user_id=user_id).order_by(GuidanceSession.created_at.desc()).all()
    return jsonify([i.to_dict() for i in items]), 200


@guidance_bp.route("", methods=["POST"])
@jwt_required()
def create_session():
    user_id = get_jwt_identity()
    data = request.get_json(force=True)
    for field in ("topic", "preferred_date"):
        if not data.get(field):
            return jsonify({"error": f"{field} is required"}), 400

    gs = GuidanceSession(
        user_id=user_id,
        topic=data["topic"],
        preferred_date=data["preferred_date"],
        notes=data.get("notes", ""),
    )
    db.session.add(gs)
    db.session.commit()
    return jsonify(gs.to_dict()), 201
