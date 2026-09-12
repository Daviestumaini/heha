from flask import Blueprint, request, jsonify
from flask_jwt_extended import jwt_required, get_jwt_identity
from extensions import db
from models import Loan

loans_bp = Blueprint("loans", __name__, url_prefix="/api/loans")


@loans_bp.route("", methods=["GET"])
@jwt_required()
def list_loans():
    user_id = get_jwt_identity()
    items = Loan.query.filter_by(user_id=user_id).order_by(Loan.created_at.desc()).all()
    return jsonify([i.to_dict() for i in items]), 200


@loans_bp.route("", methods=["POST"])
@jwt_required()
def create_loan():
    user_id = get_jwt_identity()
    data = request.get_json(force=True)
    for field in ("amount_requested", "purpose", "term_months"):
        if not data.get(field):
            return jsonify({"error": f"{field} is required"}), 400

    loan = Loan(
        user_id=user_id,
        amount_requested=float(data["amount_requested"]),
        purpose=data["purpose"],
        term_months=int(data["term_months"]),
    )
    db.session.add(loan)
    db.session.commit()
    return jsonify(loan.to_dict()), 201
