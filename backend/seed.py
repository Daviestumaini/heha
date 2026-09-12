"""Run once to populate sample products: python seed.py"""
from app import create_app
from extensions import db
from models import Product

app = create_app()

SAMPLE_PRODUCTS = [
    # Merchandise
    {"category": "merchandise", "name": "HEHA Branded T-Shirt", "description": "100% cotton, unisex fit.",
     "price": 15.0, "stock": 50, "image_url": "/assets/img/tshirt.jpg"},
    {"category": "merchandise", "name": "HEHA Tote Bag", "description": "Durable canvas moving/shopping tote.",
     "price": 8.0, "stock": 80, "image_url": "/assets/img/tote.jpg"},
    # Phones
    {"category": "phone", "name": "Nova X12 Smartphone", "description": "6.5in display, 128GB, dual SIM.",
     "price": 249.0, "stock": 20, "image_url": "/assets/img/nova_x12.jpg"},
    {"category": "phone", "name": "Nova Lite 5", "description": "Budget-friendly, 64GB, 5000mAh battery.",
     "price": 129.0, "stock": 35, "image_url": "/assets/img/nova_lite5.jpg"},
    # Cases
    {"category": "case", "name": "Shockproof Case - Universal", "description": "Fits most 6.1-6.7in phones.",
     "price": 6.5, "stock": 100, "image_url": "/assets/img/case_shock.jpg"},
    {"category": "case", "name": "Leather Wallet Case", "description": "Card slots + magnetic clasp.",
     "price": 12.0, "stock": 60, "image_url": "/assets/img/case_wallet.jpg"},
]

with app.app_context():
    db.create_all()
    if Product.query.count() == 0:
        for p in SAMPLE_PRODUCTS:
            db.session.add(Product(**p))
        db.session.commit()
        print(f"Seeded {len(SAMPLE_PRODUCTS)} products.")
    else:
        print("Products table already has data; skipping seed.")
