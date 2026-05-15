<?php
// about.php
// Joe's Electronics — About / Our Story page

session_start();
include "connect.php";
include "navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>About Us - Joe's Electronics</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ---- HERO ---- */
        .about-hero {
            background: linear-gradient(135deg, var(--text-primary) 0%, #1a1a4e 60%, #2d1b69 100%);
            border-radius: var(--radius-xl);
            padding: 80px 60px;
            margin-bottom: 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.7s var(--ease-out);
        }
        .about-hero::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(67,97,238,0.25) 0%, transparent 70%);
            top: -150px; right: -150px;
            border-radius: 50%;
        }
        .about-hero::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(124,58,237,0.2) 0%, transparent 70%);
            bottom: -100px; left: -80px;
            border-radius: 50%;
        }
        .about-hero-emoji {
            font-size: 64px;
            display: block;
            margin-bottom: 24px;
            position: relative; z-index: 1;
            animation: bounce 2s ease-in-out infinite;
        }
        .about-hero h1 {
            font-size: 52px;
            font-weight: 800;
            color: white;
            letter-spacing: -2px;
            margin-bottom: 16px;
            position: relative; z-index: 1;
        }
        .about-hero p {
            font-size: 18px;
            color: rgba(255,255,255,0.6);
            max-width: 560px;
            margin: 0 auto;
            position: relative; z-index: 1;
        }

        /* ---- SECTION LABEL ---- */
        .section-eyebrow {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--accent);
            margin-bottom: 10px;
            display: block;
        }

        /* ---- TWO-COL LAYOUT ---- */
        .about-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
            animation: fadeInUp 0.6s var(--ease-out) 0.1s backwards;
        }
        @media (max-width: 768px) {
            .about-two-col { grid-template-columns: 1fr; }
            .about-hero { padding: 48px 28px; }
            .about-hero h1 { font-size: 36px; }
        }

        /* ---- FOUNDER CARD ---- */
        .founder-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: all 0.3s var(--ease-out);
        }
        .founder-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }
        .founder-avatar {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
        }
        .founder-name {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .founder-role {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            margin-bottom: 20px;
        }
        .founder-bio {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.8;
            margin: 0;
        }

        /* ---- TIMELINE ---- */
        .timeline-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px;
            transition: all 0.3s var(--ease-out);
        }
        .timeline-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }
        .timeline {
            position: relative;
            padding-left: 32px;
            margin-top: 8px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 7px; top: 8px; bottom: 8px;
            width: 2px;
            background: linear-gradient(180deg, var(--accent), #7c3aed, var(--success));
            border-radius: 2px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 28px;
            animation: slideInLeft 0.5s var(--ease-out) backwards;
        }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -28px;
            top: 6px;
            width: 12px; height: 12px;
            background: var(--accent);
            border-radius: 50%;
            border: 3px solid var(--bg-primary);
            box-shadow: 0 0 0 2px var(--accent);
            transition: transform 0.3s var(--ease-spring);
        }
        .timeline-item:hover::before {
            transform: scale(1.4);
        }
        .timeline-year {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--accent);
            margin-bottom: 4px;
        }
        .timeline-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .timeline-desc {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ---- VALUES ---- */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 60px;
            animation: fadeInUp 0.6s var(--ease-out) 0.2s backwards;
        }
        @media (max-width: 768px) {
            .values-grid { grid-template-columns: 1fr; }
        }
        .value-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px 28px;
            text-align: center;
            transition: all 0.3s var(--ease-out);
            position: relative;
            overflow: hidden;
        }
        .value-card::before {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            transform: scaleX(0);
            transition: transform 0.3s var(--ease-out);
        }
        .value-card:nth-child(1)::before { background: var(--accent); }
        .value-card:nth-child(2)::before { background: var(--success); }
        .value-card:nth-child(3)::before { background: var(--warning); }
        .value-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }
        .value-card:hover::before { transform: scaleX(1); }
        .value-icon {
            font-size: 40px;
            margin-bottom: 16px;
            display: block;
        }
        .value-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        .value-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* ---- STATS ROW ---- */
        .about-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: 60px;
            animation: fadeInUp 0.6s var(--ease-out) 0.3s backwards;
        }
        @media (max-width: 768px) {
            .about-stats { grid-template-columns: repeat(2, 1fr); }
        }
        .about-stat {
            padding: 36px 24px;
            text-align: center;
            border-right: 1px solid var(--border);
            transition: background 0.3s var(--ease-out);
        }
        .about-stat:last-child { border-right: none; }
        .about-stat:hover { background: var(--bg-secondary); }
        .about-stat-num {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -1.5px;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }
        .about-stat-lbl {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
        }

        /* ---- BACK BUTTON ROW ---- */
        .about-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 40px;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            margin-bottom: 60px;
            animation: fadeInUp 0.6s var(--ease-out) 0.4s backwards;
        }
        .about-footer-row p {
            margin: 0;
            font-size: 15px;
            color: var(--text-secondary);
        }
        .about-footer-row strong {
            color: var(--text-primary);
        }
    </style>
</head>
<body>

<div class="box">

    <!-- HERO -->
    <div class="about-hero">
        <span class="about-hero-emoji">⚡</span>
        <h1>Joe's Electronics</h1>
        <p>From a garage in Almaty to your favourite electronics shop — here's our story.</p>
    </div>

    <!-- FOUNDER + TIMELINE -->
    <div class="about-two-col">

        <div class="founder-card">
            <div class="founder-avatar">👨‍💼</div>
            <span class="section-eyebrow">Meet the Founder</span>
            <div class="founder-name">Joe Mitchell</div>
            <div class="founder-role">Founder & CEO</div>
            <p class="founder-bio">
                Joe Mitchell grew up in Portland, Oregon, where he spent his teenage years disassembling
                every gadget he could get his hands on — much to his parents' frustration. After studying
                Computer Engineering, a chance backpacking trip through Central Asia led him to Almaty,
                where he fell in love with the city and never quite left. Noticing that quality electronics
                were either hard to find or sold at unfair markups, Joe saw an opportunity. In 2015 he
                cleared out a spare garage, bought a folding table, and opened Joe's Electronics with a
                single box of stock. Today he still personally reviews every product before it enters the
                catalogue, still replies to customer emails himself, and still eats apple pie for breakfast
                whenever he gets the chance. The original garage table now sits in the corner of the office
                as a reminder of where it all started.
            </p>
        </div>

        <div class="timeline-card">
            <span class="section-eyebrow">Our History</span>
            <h3 style="margin-top:4px; padding-left:0;">How we got here</h3>
            <div class="timeline">
                <div class="timeline-item" style="animation-delay:0.1s">
                    <div class="timeline-year">2015</div>
                    <div class="timeline-title">🏠 Garage Startup</div>
                    <div class="timeline-desc">Joe opens a one-man operation from his garage in Taldykorgan, selling phones and cables out of a single cardboard box.</div>
                </div>
                <div class="timeline-item" style="animation-delay:0.2s">
                    <div class="timeline-year">2017</div>
                    <div class="timeline-title">🏪 First Real Shop</div>
                    <div class="timeline-desc">Rented a tiny 20 m² unit in the central bazaar. First employee hired — Joe's cousin Aibek, who still works here.</div>
                </div>
                <div class="timeline-item" style="animation-delay:0.3s">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-title">💻 Going Digital</div>
                    <div class="timeline-desc">Launched online sales during the pandemic. Discovered that customers from across Kazakhstan wanted what we had.</div>
                </div>
                <div class="timeline-item" style="animation-delay:0.4s">
                    <div class="timeline-year">2023</div>
                    <div class="timeline-title">📦 New Warehouse</div>
                    <div class="timeline-desc">Moved to a proper warehouse with a showroom. Stock expanded to 500+ products. First MacBook sold in under 10 minutes.</div>
                </div>
                <div class="timeline-item" style="animation-delay:0.5s">
                    <div class="timeline-year">2026</div>
                    <div class="timeline-title">🚀 Today</div>
                    <div class="timeline-desc">Serving 15 000+ happy customers, still at honest prices, still hand-picking every single product. The garage table is now in the office as a reminder.</div>
                </div>
            </div>
        </div>

    </div>

    <!-- VALUES -->
    <span class="section-eyebrow" style="display:block; text-align:center; margin-bottom:20px;">What we stand for</span>
    <div class="values-grid">
        <div class="value-card">
            <span class="value-icon">🤝</span>
            <div class="value-title">Honesty</div>
            <div class="value-desc">No hidden fees, no fake discounts. The price you see is the price you pay, and we'll always tell you if a cheaper option exists.</div>
        </div>
        <div class="value-card">
            <span class="value-icon">✅</span>
            <div class="value-title">Quality</div>
            <div class="value-desc">Every product is sourced from authorised distributors. We test before we sell and stand behind everything in our catalogue.</div>
        </div>
        <div class="value-card">
            <span class="value-icon">🏘️</span>
            <div class="value-title">Community</div>
            <div class="value-desc">We're a local shop that knows its customers by name. Your support keeps jobs in the city and money in the region.</div>
        </div>
    </div>

    <!-- STATS -->
    <div class="about-stats">
        <div class="about-stat">
            <div class="about-stat-num">11</div>
            <div class="about-stat-lbl">Years in Business</div>
        </div>
        <div class="about-stat">
            <div class="about-stat-num">15K+</div>
            <div class="about-stat-lbl">Happy Customers</div>
        </div>
        <div class="about-stat">
            <div class="about-stat-num">500+</div>
            <div class="about-stat-lbl">Products</div>
        </div>
        <div class="about-stat">
            <div class="about-stat-num">1</div>
            <div class="about-stat-lbl">Garage Table (still here)</div>
        </div>
    </div>

    <!-- BACK TO SHOP -->
    <div class="about-footer-row">
        <p>Ready to browse? <strong>Head back to the dashboard and see what's in stock.</strong></p>
        <a href="dashboard.php" class="btn btn-blue" style="padding: 14px 32px; font-size: 15px;">← Back to Shop</a>
    </div>

</div>
</body>
</html>
