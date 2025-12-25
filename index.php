<?php
require_once 'includes/connexion.php';
require_once 'includes/header.php';
?>

<section class="hero-section">
    <div class="container hero-container">
        <div class="hero-text">
            <h1>Développez vos <br><span class="highlight">compétences</span></h1>
            <p>
                Des formations professionnelles de qualité pour booster votre carrière. 
                Apprenez à votre rythme, où que vous soyez, avec des experts du domaine.
            </p>
            <div class="hero-buttons">
                <a href="formations.php" class="btn">Découvrir nos formations</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://img.freepik.com/free-vector/online-learning-isometric-concept_1284-17947.jpg?w=800" alt="Étudiants en ligne">
        </div>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <h2 class="section-title">Nos formations phares</h2>
        
        <div class="cards-grid">
            
            <article class="card formation-preview">
                <div class="card-icon" style="background-color: #e3f2fd; color: #007bff;">
                    💻
                </div>
                <h3>Développement Web</h3>
                <p>Maîtrisez HTML, CSS, PHP et JS pour créer des sites web modernes et dynamiques.</p>
                <a href="formations.php" class="btn-link">Voir la formation &rarr;</a>
            </article>

            <article class="card formation-preview">
                <div class="card-icon" style="background-color: #fff3cd; color: #856404;">
                    📢
                </div>
                <h3>Marketing Digital</h3>
                <p>Apprenez à gérer les réseaux sociaux, le SEO et les campagnes publicitaires.</p>
                <a href="formations.php" class="btn-link">Voir la formation &rarr;</a>
            </article>

            <article class="card formation-preview">
                <div class="card-icon" style="background-color: #d4edda; color: #155724;">
                    🇬🇧
                </div>
                <h3>Anglais Business</h3>
                <p>Perfectionnez votre anglais pour évoluer dans un environnement international.</p>
                <a href="formations.php" class="btn-link">Voir la formation &rarr;</a>
            </article>

        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>