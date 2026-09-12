import os

class Config:
    """Central configuration. Override any of these with real env vars in production."""
    SECRET_KEY = os.environ.get("SECRET_KEY", "change-this-secret")
    JWT_SECRET_KEY = os.environ.get("JWT_SECRET_KEY", "change-this-jwt-secret")
    SQLALCHEMY_DATABASE_URI = os.environ.get("DATABASE_URL", "sqlite:///heha.db")
    SQLALCHEMY_TRACK_MODIFICATIONS = False
