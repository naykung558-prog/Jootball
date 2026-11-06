<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">
    <title><?= lang('Privacy.privacy_policy') ?> - Jootball</title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/styles.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/myStyle.css') ?>">
    <!-- Font Awesome 6 for modern icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/css/kanit.css') ?>">

    <style>
        :root {
            --coach-primary: #0B1548;
            --coach-secondary: #F3B819;
            --coach-dark: #000000;
            --coach-light: #ffffff;
            --coach-gray-100: #f8f9fa;
            --coach-gray-200: #e9ecef;
            --coach-gray-300: #dee2e6;
            --coach-shadow: 0 2px 10px rgba(11, 21, 72, 0.1);
            --coach-shadow-lg: 0 4px 20px rgba(11, 21, 72, 0.15);
            --coach-border-radius: 8px;
            --coach-transition: all 0.3s ease;
        }

        body.kanit-regular {
            background-color: var(--coach-gray-100);
            font-family: 'Kanit', sans-serif;
        }

        /* Header Styles */
        .coach-header {
            box-shadow: var(--coach-shadow);
            background-color: var(--coach-primary);
        }

        .coach-navbar {
            background-color: var(--coach-primary) !important;
            padding: 1rem 0;
        }

        .coach-logo {
            transition: var(--coach-transition);
        }

        .coach-logo:hover {
            transform: scale(1.05);
        }

        /* Privacy Content Styles */
        .privacy-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .privacy-card {
            background: var(--coach-light);
            border-radius: var(--coach-border-radius);
            box-shadow: var(--coach-shadow-lg);
            padding: 40px;
            margin-bottom: 30px;
        }

        .privacy-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 3px solid var(--coach-secondary);
        }

        .privacy-header h1 {
            color: var(--coach-primary);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .privacy-header .last-updated {
            color: var(--coach-gray-600);
            font-size: 0.95rem;
        }

        .privacy-section {
            margin-bottom: 35px;
        }

        .privacy-section h2 {
            color: var(--coach-primary);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--coach-gray-200);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .privacy-section h2 i {
            color: var(--coach-secondary);
        }

        .privacy-section h3 {
            color: var(--coach-gray-700);
            font-size: 1.4rem;
            font-weight: 600;
            margin: 25px 0 15px 0;
        }

        .privacy-section p {
            color: var(--coach-gray-600);
            line-height: 1.8;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .privacy-section ul,
        .privacy-section ol {
            color: var(--coach-gray-600);
            line-height: 2;
            margin-bottom: 20px;
            padding-left: 30px;
        }

        .privacy-section li {
            margin-bottom: 10px;
        }

        .privacy-section strong {
            color: var(--coach-primary);
            font-weight: 600;
        }

        .privacy-highlight {
            background-color: rgba(243, 184, 25, 0.1);
            border-left: 4px solid var(--coach-secondary);
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .privacy-contact {
            background: linear-gradient(135deg, var(--coach-primary) 0%, #1e3a8a 100%);
            color: var(--coach-light);
            padding: 30px;
            border-radius: var(--coach-border-radius);
            margin-top: 40px;
        }

        .privacy-contact h3 {
            color: var(--coach-light);
            margin-bottom: 20px;
        }

        .privacy-contact p {
            color: var(--coach-gray-200);
            margin-bottom: 10px;
        }

        .privacy-contact a {
            color: var(--coach-secondary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--coach-transition);
        }

        .privacy-contact a:hover {
            color: var(--coach-light);
        }

        .privacy-contact .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .privacy-contact .contact-item i {
            color: var(--coach-secondary);
            font-size: 1.2rem;
        }

        /* Footer */
        .privacy-footer {
            text-align: center;
            padding: 30px 0;
            color: var(--coach-gray-600);
            border-top: 1px solid var(--coach-gray-200);
            margin-top: 40px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .privacy-card {
                padding: 25px;
            }

            .privacy-header h1 {
                font-size: 1.8rem;
            }

            .privacy-section h2 {
                font-size: 1.5rem;
            }

            .privacy-section h3 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body class="kanit-regular">
    <!-- Header -->
    <header class="coach-header">
        <nav class="navbar navbar-expand-lg px-3 kanit-medium coach-navbar">
            <div class="container">
                <a class="navbar-brand" href="<?= base_url('/') ?>">
                    <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="Jootball" height="60px" class="coach-logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="fas fa-bars" style="color: var(--coach-secondary);"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/') ?>" style="color: var(--coach-light);">
                                <i class="fas fa-home"></i> <?= lang('Common.privacy_home') ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" style="color: var(--coach-light);">
                                <i class="fas fa-globe"></i> <?= lang('Common.privacy_language') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= lang_url('th') ?>">🇹🇭 ไทย</a></li>
                                <li><a class="dropdown-item" href="<?= lang_url('en') ?>">🇬🇧 English</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="privacy-container">
        <div class="privacy-card">

            <?php if(session()->get('language')=='th'){?>
                <!-- Thai Version -->
                <div class="privacy-header">
                    <h1><i class="fas fa-shield-alt"></i> ประกาศการคุ้มครองข้อมูลส่วนบุคคล (PDPA)</h1>
                    <p class="last-updated">
                        <?php
                        $months_th = [
                            'January' => 'มกราคม', 'February' => 'กุมภาพันธ์', 'March' => 'มีนาคม',
                            'April' => 'เมษายน', 'May' => 'พฤษภาคม', 'June' => 'มิถุนายน',
                            'July' => 'กรกฎาคม', 'August' => 'สิงหาคม', 'September' => 'กันยายน',
                            'October' => 'ตุลาคม', 'November' => 'พฤศจิกายน', 'December' => 'ธันวาคม'
                        ];
                        $date_en = date('j F Y', strtotime('+543 years'));
                        $date_th = str_replace(array_keys($months_th), array_values($months_th), $date_en);
                        ?>
                        อัพเดตล่าสุด: <?= $date_th ?>
                    </p>
                </div>

                <div class="privacy-section">
                    <p>
                        <strong>Jootball</strong> ตระหนักถึงความสำคัญของข้อมูลส่วนบุคคลและข้อมูลอื่นอันเกี่ยวกับท่าน
                        (รวมเรียกว่า "ข้อมูล") เพื่อให้ท่านสามารถเชื่อมั่นได้ว่า Jootball มีความโปร่งใสและความรับผิดชอบ
                        ในการเก็บรวบรวม ใช้ หรือเปิดเผยข้อมูลของท่าน ตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562
                        ("กฎหมายคุ้มครองข้อมูลส่วนบุคคล") รวมถึงกฎหมายอื่นที่เกี่ยวข้อง
                    </p>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-info-circle"></i> ข้อมูลส่วนบุคคลคืออะไร</h2>
                    <p>
                        ข้อมูลส่วนบุคคล หมายถึง ข้อมูลเกี่ยวกับบุคคลซึ่งทำให้สามารถระบุตัวบุคคลนั้นได้
                        ไม่ว่าทางตรงหรือทางอ้อม แต่ไม่รวมถึงข้อมูลของผู้ถึงแก่กรรมโดยเฉพาะ
                    </p>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-database"></i> Jootball เก็บข้อมูลของคุณประเภทใดบ้าง</h2>
                    <p>ข้อมูลที่อาจมีการเก็บรวบรวม ได้แก่</p>

                    <h3>1. ข้อมูลส่วนบุคคลทั่วไป</h3>
                    <ul>
                        <li>คำนำหน้าชื่อ ชื่อ นามสกุล</li>
                        <li>เลขบัตรประชาชน สัญชาติ เลขหนังสือเดินทาง</li>
                        <li>ข้อมูลทะเบียนบ้าน</li>
                        <li>หมายเลขใบอนุญาตต่าง ๆ</li>
                    </ul>

                    <h3>2. ข้อมูลส่วนบุคคลที่มีความละเอียดอ่อน</h3>
                    <ul>
                        <li>เชื้อชาติ ศาสนา ความพิการ</li>
                        <li>ความเห็นทางการเมือง</li>
                        <li>ประวัติอาชญากรรม</li>
                        <li>ข้อมูลชีวภาพ (ภาพใบหน้า)</li>
                        <li>ข้อมูลสุขภาพ</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-search"></i> Jootball เก็บข้อมูลผ่านช่องทางใด</h2>
                    <ol>
                        <li>จากเจ้าของข้อมูลโดยตรงผ่านช่องทางการให้บริการ</li>
                        <li>ผ่านการเข้าใช้งานเว็บไซต์</li>
                        <li>จากแหล่งข้อมูลอื่น เช่น การเชื่อมโยงบริการดิจิทัลของหน่วยงานรัฐ</li>
                    </ol>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-bullseye"></i> วัตถุประสงค์ในการเก็บข้อมูล</h2>
                    <ol>
                        <li>เพื่อดำเนินภารกิจเพื่อประโยชน์สาธารณะหรือการใช้อำนาจรัฐ</li>
                        <li>เพื่อปฏิบัติตามกฎหมายที่เกี่ยวข้อง</li>
                        <li>เพื่อประโยชน์โดยชอบด้วยกฎหมายของ Jootball</li>
                        <li>เพื่อปฏิบัติตามสัญญาหรือดำเนินการก่อนเข้าทำสัญญา</li>
                    </ol>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-share-alt"></i> การเปิดเผยข้อมูล</h2>
                    <ul>
                        <li>หน่วยงานของรัฐตามกฎหมาย</li>
                        <li>สำนักงานคณะกรรมการคุ้มครองข้อมูลส่วนบุคคล</li>
                        <li>ผู้ให้บริการที่ประมวลผลข้อมูลแทน Jootball</li>
                        <li>หน่วยงานที่มีอำนาจตามกฎหมายอื่น</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-user-check"></i> สิทธิของเจ้าของข้อมูล</h2>
                    <ul>
                        <li>สิทธิขอเข้าถึงข้อมูล</li>
                        <li>สิทธิขอแก้ไขข้อมูลให้ถูกต้อง</li>
                        <li>สิทธิขอลบหรือทำลายข้อมูล</li>
                        <li>สิทธิขอระงับการใช้ข้อมูล</li>
                        <li>สิทธิคัดค้านการประมวลผลข้อมูล</li>
                        <li>สิทธิถอนความยินยอม</li>
                    </ul>
                </div>

                <div class="privacy-highlight">
                    <p style="margin-bottom: 0;">
                        <strong>📄 ท่านสามารถอ่านรายละเอียดฉบับเต็มได้ที่:</strong><br>
                        <a href="#" style="color: var(--coach-primary); font-weight: 600;">
                            ประกาศการคุ้มครองข้อมูลส่วนบุคคลฉบับเต็มของ Jootball
                        </a>
                    </p>
                </div>

                <div class="privacy-contact">
                    <h3><i class="fas fa-headset"></i> ติดต่อสอบถาม</h3>
                    <p>หากมีข้อสงสัยเกี่ยวกับนโยบายนี้ สามารถติดต่อได้ที่</p>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>อีเมล: <a href="mailto:jootball@gmail.com">jootball@gmail.com</a></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>โทรศัพท์: 086-586-2599</span>
                    </div>
                </div>

            <?php } else { ?>
                <!-- English Version -->
                <div class="privacy-header">
                    <h1><i class="fas fa-shield-alt"></i> Privacy Notice (PDPA)</h1>
                    <p class="last-updated">Last Updated: <?= date('F j, Y'); ?></p>
                </div>

                <div class="privacy-section">
                    <p>
                        <strong>Jootball</strong> recognizes the importance of personal data protection and is committed to ensuring
                        transparency and accountability in the collection, use, and disclosure of personal data in accordance with
                        the Personal Data Protection Act B.E. 2562 (2019) ("PDPA") and other applicable laws.
                    </p>
                    <p>
                        This Privacy Notice explains how Jootball collects, uses, discloses, and processes personal data,
                        including the roles of its officers, employees, and authorized persons acting on its behalf.
                    </p>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-info-circle"></i> Definition of Personal Data</h2>
                    <p>
                        "Personal Data" means any information relating to an individual which enables the identification of such
                        individual, whether directly or indirectly, but excludes information relating specifically to deceased persons.
                    </p>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-database"></i> Categories of Personal Data Collected</h2>

                    <h3>1. General Personal Data</h3>
                    <ul>
                        <li>Title, first name, last name, middle name, nickname</li>
                        <li>Signature</li>
                        <li>National identification number</li>
                        <li>Nationality</li>
                        <li>Driver's license number</li>
                        <li>Passport number</li>
                        <li>Household registration information</li>
                        <li>Business registration number</li>
                        <li>Professional license number</li>
                        <li>Insured person number</li>
                        <li>Social security number</li>
                    </ul>

                    <h3>2. Sensitive Personal Data</h3>
                    <p>Where necessary and permitted by law, Jootball may collect sensitive personal data, including:</p>
                    <ul>
                        <li>Racial or ethnic data</li>
                        <li>Religious information</li>
                        <li>Disability information</li>
                        <li>Political opinions</li>
                        <li>Criminal records</li>
                        <li>Biometric data (e.g., facial recognition data)</li>
                        <li>Genetic data</li>
                        <li>Health information</li>
                    </ul>
                    <div class="privacy-highlight">
                        <p style="margin-bottom: 0;">
                            Sensitive Personal Data will be collected only where strictly necessary and with explicit consent
                            or other lawful basis under the PDPA.
                        </p>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-search"></i> Sources of Personal Data</h2>
                    <ul>
                        <li>Directly from you through our service channels</li>
                        <li>Through your use of our website</li>
                        <li>From third parties or external sources, including government digital service linkages, where legally permitted</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-bullseye"></i> Purposes and Legal Bases for Processing</h2>

                    <h3>1. Public Interest or Official Authority</h3>
                    <p>
                        To perform tasks carried out in the public interest or in the exercise of official authority vested in
                        Jootball under applicable laws, regulations, orders, or Cabinet resolutions.
                    </p>

                    <h3>2. Legal Obligation</h3>
                    <p>
                        To comply with legal obligations to which Jootball is subject.
                    </p>

                    <h3>3. Legitimate Interests</h3>
                    <p>
                        For the legitimate interests of Jootball or third parties, provided such interests are not overridden
                        by the fundamental rights of the data subject (e.g., user account registration and system access).
                    </p>

                    <h3>4. Contractual Necessity</h3>
                    <p>
                        For the performance of a contract to which you are a party,
                        or to take steps at your request prior to entering into a contract.
                    </p>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-share-alt"></i> Disclosure of Personal Data</h2>
                    <ul>
                        <li>Government agencies authorized under the PDPA</li>
                        <li>Personal data regulatory authorities (e.g., the Office of the Personal Data Protection Committee)</li>
                        <li>Data processors or service providers engaged by Jootball</li>
                        <li>Other legally authorized entities for audit or compliance purposes</li>
                    </ul>
                    <p>All disclosures will be made on a need-to-know basis and subject to appropriate safeguards.</p>
                </div>

                <div class="privacy-section">
                    <h2><i class="fas fa-user-check"></i> Your Rights as a Data Subject</h2>
                    <ul>
                        <li>Right of access</li>
                        <li>Right to rectification</li>
                        <li>Right to erasure</li>
                        <li>Right to restriction of processing</li>
                        <li>Right to object</li>
                        <li>Right to withdraw consent</li>
                        <li>Right to data portability</li>
                    </ul>
                    <p>Requests to exercise these rights will be handled in accordance with applicable law.</p>
                </div>

                <div class="privacy-highlight">
                    <p style="margin-bottom: 0;">
                        <strong>📄 You may review the full version of Jootball's Privacy Notice at:</strong><br>
                        <a href="#" style="color: var(--coach-primary); font-weight: 600;">
                            Jootball Full Privacy Notice — Click here
                        </a>
                    </p>
                </div>

                <div class="privacy-contact">
                    <h3><i class="fas fa-headset"></i> Contact Information</h3>
                    <p>If you have any questions, concerns, or requests regarding this Notice or the processing of your personal data, please contact:</p>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>Email: <a href="mailto:jootball@gmail.com">jootball@gmail.com</a></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>Telephone: 086-586-2599</span>
                    </div>
                </div>

            <?php }?>

        </div>

        <!-- Footer -->
        <div class="privacy-footer">
            <p>&copy; <?= date('Y') ?> Jootball. All rights reserved.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
