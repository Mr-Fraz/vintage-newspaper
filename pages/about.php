<?php
  echo "<h1>About Our Vintage Newspaper</h1>";
  echo "<p>Welcome to the archives.</p>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | The Vintage Gazette</title>
    <style>
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

        .content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .main-article {
            text-align: justify;
            column-count: 2;
            column-gap: 20px;
        }

        .main-article p::first-letter {
            float: left;
            font-size: 4rem;
            line-height: 1;
            padding-right: 8px;
            font-weight: bold;
        }

        h2 {
            border-bottom: 1px solid #2b2b2b;
            text-transform: uppercase;
            font-size: 1.2rem;
            margin-top: 0;
        }

        .sidebar {
            border-left: 1px solid #2b2b2b;
            padding-left: 20px;
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

            .content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .main-article {
                column-count: 1;
            }

            .sidebar {
                border-left: none;
                border-top: 1px solid #2b2b2b;
                padding-left: 0;
                padding-top: 20px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 1.6rem;
            }

            .container {
                padding: 15px;
            }

            .main-article p::first-letter {
                font-size: 2.8rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>The Vintage Gazette</h1>
        <div class="meta-data">
            <span>Vol. I — No. 001</span>
            <span><?php echo date("l, F j, Y"); ?></span>
            <span>Price: Two Cents</span>
        </div>
    </header>

    <div class="content">
        <section class="main-article">
            <h2>Our Mission: Preserving the Printed Word</h2>
            <p>This digital design portfolio serves as a living archive of historical aesthetic. In an age of rapid pixels and fleeting data, the Vintage Gazette aims to bridge the gap between modern technology and the timeless beauty of 20th-century newsprint. 

            Each layout, font choice, and digital "ink stain" is meticulously crafted to transport the viewer back to an era where information carried weight—literally and figuratively. Through this project, we explore the intersection of classical typography and modern PHP development, ensuring that the soul of the broadsheet remains alive in the digital frontier.</p>
        </section>

        <aside class="sidebar">
            <h2>The Editor's Note</h2>
            <p>Welcome to our first edition. This project is a testament to the power of design to evoke nostalgia and tell a story beyond the text itself.</p>
            <p><strong>Lead Designer:</strong> [Your Name]<br>
            <strong>Technology:</strong> PHP, CSS3, Apache</p>
        </aside>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Vintage Newspaper Project | Built for Historical Perspective</p>
    </footer>
</div>

</body>
</html>