<?php
include "config.php";
include "auth.php";
include "header.php";
?>

<div class="container">
    <div style="text-align:center; margin-bottom:40px;">
        <h2 style="font-size:28px; color:#1a237e; margin-bottom:10px;">Services</h2>
        <p style="color:#666;">You can find your desired service directly, or by the organization providing it</p>
    </div>

    <div class="service-grid">
        <a href="museums.php" class="service-card grad-yellow">
            <div class="card-content">
                <div class="card-icon">🏛️</div>
                <div>
                    <div class="card-tag">MUSEUM</div>
                    <div class="card-title">Museums List</div>
                </div>
            </div>
        </a>

        <a href="events.php" class="service-card grad-blue">
            <div class="card-content">
                <div class="card-icon">📅</div>
                <div>
                    <div class="card-tag">EVENT</div>
                    <div class="card-title">Upcoming Events</div>
                </div>
            </div>
        </a>

        <a href="galleries.php" class="service-card grad-green">
            <div class="card-content">
                <div class="card-icon">🖼️</div>
                <div>
                    <div class="card-tag">GALLERY</div>
                    <div class="card-title">Browse Galleries</div>
                </div>
            </div>
        </a>

        <a href="artists.php" class="service-card grad-teal">
            <div class="card-content">
                <div class="card-icon">🎨</div>
                <div>
                    <div class="card-tag">ARTIST</div>
                    <div class="card-title">Find Artists</div>
                </div>
            </div>
        </a>

        <a href="art_pieces.php" class="service-card grad-purple">
            <div class="card-content">
                <div class="card-icon">🏺</div>
                <div>
                    <div class="card-tag">COLLECTION</div>
                    <div class="card-title">Art Pieces</div>
                </div>
            </div>
        </a>

        <?php if(is_logged_in() && !is_admin()): ?>
        <a href="buy_tickets.php" class="service-card grad-red">
            <div class="card-content">
                <div class="card-icon">🎟️</div>
                <div>
                    <div class="card-tag">TICKET</div>
                    <div class="card-title">Buy Tickets</div>
                </div>
            </div>
        </a>
        <?php endif; ?>

        <?php if(is_admin()): ?>
        <a href="dashboard.php" class="service-card grad-blue">
            <div class="card-content">
                <div class="card-icon">📊</div>
                <div>
                    <div class="card-tag">ADMIN</div>
                    <div class="card-title">Dashboard</div>
                </div>
            </div>
        </a>
        
        <a href="tickets.php" class="service-card grad-red">
            <div class="card-content">
                <div class="card-icon">🎫</div>
                <div>
                    <div class="card-tag">ADMIN</div>
                    <div class="card-title">Manage Tickets</div>
                </div>
            </div>
        </a>

        <a href="visitors.php" class="service-card grad-green">
            <div class="card-content">
                <div class="card-icon">👥</div>
                <div>
                    <div class="card-tag">ADMIN</div>
                    <div class="card-title">Visitor Log</div>
                </div>
            </div>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php include "footer.php"; ?>
