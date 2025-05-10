// This script fetches and displays product ratings on the homepage

document.addEventListener('DOMContentLoaded', function() {
    // Add star rating CSS
    const style = document.createElement('style');
    style.textContent = `
        .star-rating {
            color: #ffc107;
            font-size: 0.9rem;
            margin: 8px 0;
        }
        .star-rating .text-muted {
            font-size: 0.8rem;
            color: #6c757d !important;
        }
    `;
    document.head.appendChild(style);
    
    // Find all product cards
    const productCards = document.querySelectorAll('.product-card');
    
    // Process each product card
    productCards.forEach(card => {
        // Get product ID from the form
        const form = card.querySelector('form');
        if (!form) return;
        
        const productIdInput = form.querySelector('input[name="product_id"]');
        if (!productIdInput) return;
        
        const productId = productIdInput.value;
        const cardBody = card.querySelector('.card-body');
        const titleElement = card.querySelector('.card-title');
        
        if (cardBody && titleElement) {
            // Create a placeholder for the rating
            const ratingDiv = document.createElement('div');
            ratingDiv.className = 'star-rating';
            ratingDiv.innerHTML = '<span class="text-muted">Loading rating...</span>';
            
            // Insert after title
            titleElement.after(ratingDiv);
            
            // Fetch rating data
            fetch(`includes/get_ratings.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        ratingDiv.innerHTML = '<span class="text-muted">Rating unavailable</span>';
                        return;
                    }
                    
                    if (!data.has_ratings) {
                        ratingDiv.innerHTML = '<span class="text-muted">No reviews yet</span>';
                        return;
                    }
                    
                    // Create star rating display
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= Math.round(data.avg_rating)) {
                            starsHtml += '<i class="fas fa-star"></i>';
                        } else {
                            starsHtml += '<i class="far fa-star"></i>';
                        }
                    }
                    
                    ratingDiv.innerHTML = starsHtml + ` <span class="text-muted">(${data.avg_rating})</span>`;
                })
                .catch(error => {
                    console.error('Error fetching rating:', error);
                    ratingDiv.innerHTML = '<span class="text-muted">Rating unavailable</span>';
                });
        }
    });
});
