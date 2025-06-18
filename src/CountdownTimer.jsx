import { useState, useEffect } from 'react';
import './CountdownTimer.css';

const CountdownTimer = () => {
  const [timeLeft, setTimeLeft] = useState({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0
  });

  useEffect(() => {
    const targetDate = new Date(window.wpCountdownTimer.targetDate);
    const redirectUrl = window.wpCountdownTimer.redirectUrl;
    
    const updateCountdown = () => {
      const now = new Date();
      const diff = targetDate - now;

      if (diff <= 0) {
        clearInterval(timer);
        if (redirectUrl) {
          window.location.href = redirectUrl;
        }
        return;
      }

      setTimeLeft({
        days: Math.floor(diff / (1000 * 60 * 60 * 24)),
        hours: Math.floor(diff / (1000 * 60 * 60)) % 24,
        minutes: Math.floor(diff / (1000 * 60)) % 60,
        seconds: Math.floor(diff / 1000) % 60
      });
    };

    const timer = setInterval(updateCountdown, 1000);
    updateCountdown();

    return () => clearInterval(timer);
  }, []);

  useEffect(() => {
    const handleDocumentClick = (event) => {
      // Check if the clicked element or any of its parents is a link - we still want to allow user to click these!
      let element = event.target;
      while (element) {
        if (element.tagName === 'A' || element.tagName === 'BUTTON' || element.tagName === 'INPUT') {
          return;
        }
        element = element.parentElement;
      }

      // If we get here, the user clicked on a part of the page with the countdown that wasn't on a link or button - redirect!
      const redirectUrl = window.wpCountdownTimer.redirectUrl;
      if (redirectUrl) {
        window.location.href = redirectUrl;
      }
    };

    // Add click listener to the document
    document.addEventListener('click', handleDocumentClick);

    // Cleanup
    return () => {
      document.removeEventListener('click', handleDocumentClick);
    };
  }, []);

  return (
    <div className="wp-countdown-timer">
      <div className="countdown-row">
        <div className="time-segment">
          <span className="time-value">{timeLeft.days}</span>
          <span className="time-label">Days</span>
        </div>
        <div className="time-segment">
          <span className="time-value">{timeLeft.hours}</span>
          <span className="time-label">Hours</span>
        </div>
        <div className="time-segment">
          <span className="time-value">{timeLeft.minutes}</span>
          <span className="time-label">Minutes</span>
        </div>
        <div className="time-segment">
          <span className="time-value">{timeLeft.seconds}</span>
          <span className="time-label">Seconds</span>
        </div>
      </div>
    </div>
  );
};

export default CountdownTimer;