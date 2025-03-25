window.addEventListener("scroll", function () {
    let navbar = document.querySelector(".navbar");
    if (window.scrollY > 50) {
      navbar.classList.add("scroll"); // Add transparent effect
    } else {
      navbar.classList.remove("scroll"); // Restore navy background
    }
  });

// Function to toggle the dropdown visibility when clicked
function toggleDropdown(event, dropdownId) {
    event.preventDefault(); // Prevent the link from navigating
  
    // Get all dropdowns
    var allDropdowns = document.querySelectorAll('.dropdown-content');
    
    // Loop through and hide all dropdowns, except the one that was clicked
    allDropdowns.forEach(function(dropdown) {
      if (dropdown.id !== dropdownId) {
        dropdown.style.display = 'none';
      }
    });
  
    // Toggle the visibility of the selected dropdown
    var dropdown = document.getElementById(dropdownId);
    if (dropdown.style.display === 'block') {
      dropdown.style.display = 'none';
    } else {
      dropdown.style.display = 'block';
    }
  }
  
  // Close dropdown if clicked outside of the dropdown
  window.onclick = function(event) {
    if (!event.target.matches('.dropbtn') && !event.target.closest('.dropdown')) {
      var dropdowns = document.querySelectorAll('.dropdown-content');
      dropdowns.forEach(function(dropdown) {
        dropdown.style.display = 'none';
      });
    }
  }
  

  let slideIndex = 0;
  const slidesToShow = 4;
  const slides = document.querySelectorAll(".slide");
  const totalSlides = slides.length;
  const slideTrack = document.querySelector(".slide-track");
  const slideWidth = 100 / slidesToShow; // Each slide takes a fraction of the container
  
  // Set initial position
  slideTrack.style.transform = `translateX(0%)`;
  
  // Function to move slides automatically
  function showSlides() {
      slideIndex++;
      if (slideIndex > totalSlides - slidesToShow) {
          // Reset to the first set of slides when reaching the end
          slideIndex = 0;
      }
      updateSlidePosition();
  }
  
  // Manual Slide Navigation
  function moveSlides(n) {
      slideIndex += n;
  
      if (slideIndex < 0) {
          slideIndex = totalSlides - slidesToShow;
      } else if (slideIndex > totalSlides - slidesToShow) {
          slideIndex = 0;
      }
  
      updateSlidePosition();
  }
  
  // Function to update slide position
  function updateSlidePosition() {
      slideTrack.style.transition = "transform 0.5s ease-in-out";
      slideTrack.style.transform = `translateX(-${slideIndex * slideWidth}%)`;
  }
  
  // Auto slide every 5 seconds
  setInterval(showSlides, 5000);

  document.addEventListener("DOMContentLoaded", function () {
    var scrollToTopBtn = document.getElementById("scrollToTop");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 300) { // Show button after scrolling down
            scrollToTopBtn.style.opacity = "1";
            scrollToTopBtn.style.visibility = "visible";
        } else {
            scrollToTopBtn.style.opacity = "0";
            scrollToTopBtn.style.visibility = "hidden";
        }
    });

    // Smooth scroll effect
    scrollToTopBtn.addEventListener("click", function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});
  