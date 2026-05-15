<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact the Gazette | The Vintage Newspaper</title>
    <style>
        /* --- Shared Vintage Styles (Same as About Page) --- */
        body {
            background-color: #f4ecd8; /* Aged paper */
            color: #2b2b2b;
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 40px;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: auto;
            border: 2px solid #2b2b2b;
            padding: 20px;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
            background-color: #fcf8ed; /* Slightly lighter inner paper */
        }

        header {
            text-align: center;
            border-bottom: 4px double #2b2b2b;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        h1 {
            font-size: 3.5rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: -2px;
        }

        .meta-data {
            border-top: 1px solid #2b2b2b;
            border-bottom: 1px solid #2b2b2b;
            padding: 5px 0;
            margin: 10px 0;
            font-style: italic;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        /* --- Contact Page Specific Styles --- */
        .contact-header {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid #2b2b2b;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .classified-section {
            border: 1px solid #2b2b2b;
            padding: 20px;
            position: relative;
        }

        .classified-section h2 {
            font-size: 1.5rem;
            margin-top: 0;
            text-align: center;
            text-transform: uppercase;
            background-color: #2b2b2b;
            color: #f4ecd8;
            padding: 5px;
        }

        .contact-method {
            margin-bottom: 15px;
            border-bottom: 1px dashed #2b2b2b;
            padding-bottom: 10px;
        }

        .contact-method:last-child {
            border-bottom: none;
        }

        .contact-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
            display: block;
        }

        .contact-value {
            font-size: 1.1rem;
        }

        .contact-value a {
            color: #2b2b2b;
            text-decoration: none;
            border-bottom: 1px solid #2b2b2b;
        }

        .contact-value a:hover {
            background-color: #2b2b2b;
            color: #f4ecd8;
        }

        /* Call to Action Box */
        .cta-box {
            grid-column: 1 / -1; /* Spans full width */
            text-align: center;
            border: 3px dashed #2b2b2b;
            padding: 30px;
            background-color: #fff;
        }

        .cta-box h3 {
            font-size: 2rem;
            margin-top: 0;
            font-family: 'Courier New', Courier, monospace; /* More 'typed' feel */
        }

        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.8rem;
            border-top: 1px solid #2b2b2b;
            padding-top: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            h1 {
                font-size: 2.2rem;
                letter-spacing: -1px;
            }

            .meta-data {
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }

            .contact-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .cta-box h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 1.6rem;
            }

            .contact-header h1 {
                font-size: 1.3rem;
            }

            .container {
                padding: 15px;
            }

            .classified-section {
                padding: 15px;
            }

            .classified-section h2 {
                font-size: 1.2rem;
            }

            .cta-box {
                padding: 20px;
            }

            .cta-box h3 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>The Vintage Gazette</h1>
        <div class="meta-data">
            <span>Vol. I — No. 002</span>
            <span><?php echo date("l, F j, Y"); ?></span>
            <span>Classifieds Section</span>
        </div>
    </header>

    <div class="contact-header">
        <h1>Official Directory & Inquiries</h1>
        <p>FOR THE ATTENTION OF THE EDITOR, DESIGNER, & PUBLISHER</p>
    </div>

    <div class="contact-grid">
        
        <div class="classified-section">
            <h2>Digital Dispatches</h2>
            
            <div class="contact-method">
                <span class="contact-label">Electronic Mail (Email)</span>
                <span class="contact-value"><a href="mailto:vintagepress@example.com">vintagepress@example.com</a></span>
            </div>

            <div class="contact-method">
                <span class="contact-label">World Wide Web Portfolio</span>
                <span class="contact-value"><a href="https://vintagenews.com" target="_blank">vintagenews.com</a></span>
            </div>

            <div class="contact-method">
                <span class="contact-label">Modern LinkedIn Profile</span>
                <span class="contact-value"><a href="https://linkedin.com/in/yourprofile" target="_blank">linkedin.com/vintagenews</a></span>
            </div>
        </div>

        <div class="classified-section">
            <h2>Post & Telegraph</h2>
            
            <div class="contact-method">
                <span class="contact-label">Physical Press Office</span>
                <span class="contact-value">
                    123 Press Pass Lane<br>
                    Inkwell City, IC 54321
                </span>
            </div>

            <div class="contact-method">
                <span class="contact-label">Telephone Exchanges</span>
                <span class="contact-value">KLondike 5-0199</span>
            </div>
            
            <div class="contact-method">
                <span class="contact-label">Telegraph Code</span>
                <span class="contact-value">VINTAGEPRESS-XYZ</span>
            </div>
        </div>

        <div class="cta-box">
            <h3>"Stop the Presses!"</h3>
            <p>Are you seeking a designer versed in the historical arts? Do you have an inquiry regarding a digital archive project? The Gazette is currently accepting new commissions and collaborations.</p>
            <p><strong>Do not delay—dispatch your inquiry today via the electronic mail channel listed above.</strong></p>
        </div>

    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> The Vintage Newspaper Project | All Rights Reserved | Established for Design Research</p>
    </footer>
</div>

</body>
</html>