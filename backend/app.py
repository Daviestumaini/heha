from flask import Flask, jsonify
from flask_cors import CORS

from config import Config
from extensions import db, jwt, bcrypt

from routes.auth import auth_bp
from routes.moving import moving_bp
from routes.loans import loans_bp
from routes.guidance import guidance_bp
from routes.shop import shop_bp


def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)

    db.init_app(app)
    jwt.init_app(app)
    bcrypt.init_app(app)
    CORS(app)  # allow the PHP frontend (different host/port) to call this API

    app.register_blueprint(auth_bp)
    app.register_blueprint(moving_bp)
    app.register_blueprint(loans_bp)
    app.register_blueprint(guidance_bp)
    app.register_blueprint(shop_bp)

    @app.route("/api/health")
    def health():
        return jsonify({"status": "ok", "service": "HEHA Agency API"}), 200

    with app.app_context():
        db.create_all()

    return app


app = create_app()

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
