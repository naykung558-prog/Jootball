<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Digital Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
    <style>
        :root {
            --neon-cyan: #00f2ff;
            --neon-pink: #ff00ff;
            --dark-bg: #050a15;
            --card-bg: rgba(10, 20, 40, 0.9);
        }

        body {
            /*background: radial-gradient(circle at center, #0a2533 0%, #020617 100%);*/
            background: radial-gradient(circle at center, #0dcaf0 0%, #020617 100%);
            min-height: 100vh;
            color: #ffffff;
            font-family: 'Kanit', sans-serif;
            padding: 40px 0;
        }

        /* --- Header Section with Animation --- */
        .header-section {
            position: relative;
            background: rgba(0, 242, 255, 0.1);
            border-bottom: 2px solid var(--neon-cyan);
            padding: 1.2rem;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 2px;
            overflow: hidden; /* สำหรับ Shimmer Effect */
        }

        /* แสงวิ่งผ่าน Header */
        .header-section::after {
            content: "";
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 242, 255, 0.2), transparent);
            animation: shimmer 4s infinite linear;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            30% { left: 100%; }
            100% { left: 100%; }
        }

        .glitch-text {
            display: inline-block;
            color: var(--neon-cyan);
            text-shadow: 0 0 8px rgba(0, 242, 255, 0.6);
            animation: glitch 4s infinite;
        }

        @keyframes glitch {
            0%, 90%, 100% { transform: none; text-shadow: 0 0 8px rgba(0, 242, 255, 0.6); }
            92% { transform: skewX(10deg); text-shadow: 2px 0 var(--neon-pink); }
            94% { transform: skewX(-10deg); text-shadow: -2px 0 var(--neon-cyan); }
            96% { transform: none; }
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #00ff00;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 10px #00ff00;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.3; }
            100% { opacity: 1; }
        }

        /* --- Card & Content --- */
        .main-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid rgba(0, 242, 255, 0.3);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(0, 242, 255, 0.05);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(8px);
        }

        .player-photo-wrapper {
            position: relative;
            display: inline-block;
            /* margin-bottom: 20px; */
            padding: 5px;
        }

        .player-photo {
            width: 200px;
            height: 240px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.2);
        }

        .corner-tag {
            position: absolute;
            width: 25px;
            height: 25px;
            border: 3px solid var(--neon-pink);
            z-index: 2;
        }
        .top-l { top: -2px; left: -2px; border-right: none; border-bottom: none; }
        .bot-r { bottom: -2px; right: -2px; border-left: none; border-top: none; }

        .info-box {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 242, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .label {
            color: rgba(0, 242, 255, 0.8);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .value {
            color: #ffffff;
            font-weight: 600;
        }

        .highlight-value {
            color: var(--neon-pink);
            font-family: 'Orbitron', sans-serif;
            font-size: 1.3rem;
        }

        .grid-layer {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(0, 242, 255, 0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 242, 255, 0.02) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: -1;
        }

        .stat-badge {
            display: inline-block;
            padding: 2px 12px;
            background: rgba(0, 242, 255, 0.1);
            border: 1px solid var(--neon-cyan);
            border-radius: 4px;
            font-size: 0.75rem;
            color: var(--neon-cyan);
            font-family: 'Orbitron', sans-serif;
        }
        /* คอนเทนเนอร์สำหรับธงบนรูป */
        .nation-overlay {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 5;
            background: rgba(0, 0, 0, 0.6); /* พื้นหลังจางๆ ให้ธงเด่น */
            padding: 5px;
            border-radius: 5px;
            border: 1px solid rgba(0, 242, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        /* ขนาดธงชาติ */
        .nation-overlay .fi {
            width: 35px !important;
            height: auto;
            border-radius: 2px;
            box-shadow: 0 0 10px rgba(0, 242, 255, 0.5);
        }

        /* ไอคอนกรณี OTHER */
        .nation-overlay .fa-globe {
            color: var(--neon-cyan);
            font-size: 1.5rem;
            filter: drop-shadow(0 0 5px var(--neon-cyan));
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="main-card">
                    <div class="grid-layer"></div>
                    
                    <div class="header-section d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-fingerprint me-2" style="color: var(--neon-cyan);"></i>
                            <span class="glitch-text">PLAYER BIOMETRIC DATA</span>
                        </div>
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.6);">
                            <span class="status-dot"></span> SYSTEM ACTIVE
                        </div>
                    </div>

                    <div class="p-4 p-md-5">
                        <div class="row align-items-start">
                            <div class="col-md-5 text-center mb-4">
                                <div class="player-photo-wrapper">
                                    <div class="corner-tag top-l"></div>
                                    <div class="corner-tag bot-r"></div>
                                    
                                    <div class="nation-overlay">
                                        <?php 
                                            $nation = strtoupper($player['nation'] ?? '');
                                            if ($nation === 'THAI'): ?>
                                                <span class="fi fi-th"></span>
                                            <?php elseif ($nation === 'OTHER'): ?>
                                                <i class="fas fa-globe"></i>
                                            <?php endif; ?>
                                    </div>

                                    <?php if (!empty($player['image_path'])) : ?>
                                        <img onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';" src="<?= base_url($player['image_path']) ?>" class="player-photo" alt="Profile">
                                    <?php else : ?>
                                        <img onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';" src="<?= base_url('public/img/img_profile.jpg') ?>" class="player-photo" alt="Default">
                                    <?php endif; ?>
                                </div>
                                <h2 class="mt-2 fw-bold" style="color: #fff; letter-spacing: 1px;">
                                    <?= ($player['nick_name']!='' ? '<small style="font-size: 1.1rem; color: var(--neon-pink); opacity: 0.8;">('.$player['nick_name'].')</small><br>' : '');?>
                                    <?= $player['first_name'] ?> <span style="/*color: var(--neon-cyan);*/"><?= $player['last_name'] ?></span>
                                    
                                </h2>
                                <div class="stat-badge mt-2">UUID: <?= str_pad($player['id'], 6, "0", STR_PAD_LEFT) ?></div>
                            </div>

                            <div class="col-md-7">
                                <div class="info-box">
                                    <div class="info-item">
                                        <span class="label"><i class="far fa-shield-check me-2"></i>Affiliation</span>
                                        <span class="value"><?= $player['team_name'] ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label"><i class="fas fa-map-marker-alt me-2"></i>Position</span>
                                        <span class="value" style="color: var(--neon-cyan);">
                                            <?php echo !empty($position_name_label) ? implode(' / ', $position_name_label) : '-'; ?>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label"><i class="fas fa-tshirt me-2"></i>Squad Number</span>
                                        <span class="value highlight-value">
                                            #<?= !empty($player['shirt_number']) ? $player['shirt_number'] : '--' ?>
                                        </span>
                                    </div>
                                    
                                    <div class="row mt-4">
                                        <div class="col-6 mb-3">
                                            <div class="label">Height</div>
                                            <div class="value"><?= $player['height'] ?> <small>CM</small></div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="label">Weight</div>
                                            <div class="value"><?= $player['weight'] ?> <small>KG</small></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="label">Main Foot</div>
                                            <div class="value text-uppercase" style="color: var(--neon-cyan);"><?= $player['dominant_foot'] ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="label">Body Mass (BMI)</div>
                                            <div class="value highlight-value" style="font-size: 1.1rem;"><?= $player['bmi'] ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 p-3 d-flex justify-content-center gap-4" style="border: 1px dashed rgba(0, 242, 255, 0.2); border-radius: 10px; background: rgba(0,0,0,0.2);">
                                    <small class="text-secondary">
                                        <i class="fas fa-home me-1"></i> Domicile : 
                                        <span class="text-white"><?= !empty($player['province_name']) ? $player['province_name'] : '-' ?></span>
                                    </small>
                                    <small class="text-secondary">
                                        <i class="fas fa-calendar-alt me-1"></i> Data Sync : 
                                        <span class="text-white"><?= date('d.m.Y') ?></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>