document.addEventListener('DOMContentLoaded', function() {
    console.log('Original East Loaded');

    document.querySelectorAll('.brands-control').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = button.getAttribute('data-target');
            var delta = parseInt(button.getAttribute('data-delta'), 10);
            var track = document.getElementById(targetId);
            if (!track) {
                return;
            }
            track.scrollBy({ left: delta, behavior: 'smooth' });
        });
    });
});
