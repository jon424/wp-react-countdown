import React from 'react';
import ReactDOM from 'react-dom/client';
import CountdownTimer from './CountdownTimer';
import './CountdownTimer.css';

document.addEventListener('DOMContentLoaded', () => {
  const rootElement = document.getElementById('wp-countdown-timer-root');
  if (rootElement) {
    ReactDOM.createRoot(rootElement).render(
      <React.StrictMode>
        <CountdownTimer />
      </React.StrictMode>
    );
  } else if (process.env.NODE_ENV === 'development') {
    // Only show error in development
    console.error('Countdown timer root element not found');
  }
});