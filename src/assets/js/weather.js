/**
 * Weather Widget Module
 * 
 * @author Chenkham Chowlu
 * @copyright (c) 2024-2026 Chenkham Chowlu
 */
(() => {
  'use strict';

  const LAT = 27.4745;
  const LON = 94.9110;
  const WEATHER_URL = `https://api.open-meteo.com/v1/forecast?latitude=${LAT}&longitude=${LON}&current_weather=true&hourly=temperature_2m&timezone=Asia/Kolkata&forecast_days=1`;
  const UPDATE_INTERVAL = 900000; // 15 minutes

  const getWeatherIcon = (temp) => {
    if (temp > 32) return '🌞';
    if (temp > 26) return '☀️';
    if (temp > 20) return '⛅';
    if (temp > 15) return '☁️';
    if (temp > 10) return '🌧️';
    return '⛈️';
  };

  const showLoadingState = () => {
    const currentTemp = document.getElementById('current-temp');
    const currentIcon = document.getElementById('current-icon');
    const currentTime = document.getElementById('current-time');

    if (currentTemp && currentTemp.textContent.trim() === '--°C') currentTemp.textContent = '...';
    if (currentIcon && currentIcon.textContent.trim() === '') currentIcon.textContent = '⛅';
    if (currentTime && currentTime.textContent.trim() === '--:--') {
      const now = new Date();
      currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }
  };

  const createForecastSlot = (time, temp) => {
    return `
      <div class="flex flex-col items-center justify-center p-1.5 sm:p-2 bg-slate-800/80 border border-slate-700/60 rounded-xl text-center shadow-xs">
        <span class="text-[10px] font-bold text-slate-400 mb-0.5">${time}</span>
        <span class="text-base sm:text-lg my-0.5">${getWeatherIcon(temp)}</span>
        <span class="text-xs font-black text-amber-300 font-mono">${Math.round(temp)}°</span>
      </div>
    `;
  };

  const updateWeatherDisplay = (data) => {
    try {
      const currentTemp = document.getElementById('current-temp');
      const currentIcon = document.getElementById('current-icon');
      const currentTime = document.getElementById('current-time');
      const hourlyContainer = document.getElementById('hourly-forecast');

      if (!data || !data.current_weather) throw new Error('Invalid weather data structure');

      const temp = data.current_weather.temperature;
      
      if (currentTemp) {
        currentTemp.textContent = `${Math.round(temp)}°C`;
        const weatherNote = temp > 28 ? 'Sunny in Dibrugarh · Great day for campus walks' : (temp > 20 ? 'Pleasant at Dibrugarh University · Perfect for coding in the lab' : 'Cool breeze in Dibrugarh · Hot Assam tea weather 🫖');
        currentTemp.setAttribute('title', weatherNote);
      }
      if (currentIcon) currentIcon.textContent = getWeatherIcon(temp);
      
      const now = new Date();
      if (currentTime) {
        currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
      }

      if (hourlyContainer && data.hourly && data.hourly.time && data.hourly.temperature_2m) {
        hourlyContainer.innerHTML = '';
        
        let startIndex = now.getHours();
        for (let i = 0; i < data.hourly.time.length; i++) {
          const tDate = new Date(data.hourly.time[i]);
          if (tDate.getTime() >= now.getTime()) {
            startIndex = i;
            break;
          }
        }

        for (let i = 0; i < 4; i++) {
          const index = (startIndex + i) % data.hourly.time.length;
          if (index < data.hourly.time.length) {
            const timeStr = new Date(data.hourly.time[index]).toLocaleTimeString('en-US', { hour: 'numeric', hour12: true });
            const fTemp = data.hourly.temperature_2m[index];
            hourlyContainer.innerHTML += createForecastSlot(timeStr, fTemp);
          }
        }
      }
    } catch (error) {
      console.error('Error updating weather display:', error);
    }
  };

  const fetchWeather = async () => {
    try {
      showLoadingState();
      const response = await fetch(WEATHER_URL);
      if (!response.ok) throw new Error(`Weather API error: ${response.status}`);
      const data = await response.json();
      updateWeatherDisplay(data);
    } catch (error) {
      // Graceful offline / fallback Dibrugarh weather
      const now = new Date();
      const currentTemp = document.getElementById('current-temp');
      const currentIcon = document.getElementById('current-icon');
      const currentTime = document.getElementById('current-time');
      const hourlyContainer = document.getElementById('hourly-forecast');

      if (currentTemp) currentTemp.textContent = '32°C';
      if (currentIcon) currentIcon.textContent = '☀️';
      if (currentTime) currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
      
      if (hourlyContainer) {
        hourlyContainer.innerHTML = `
          ${createForecastSlot('15:00', 33)}
          ${createForecastSlot('16:00', 31)}
          ${createForecastSlot('17:00', 29)}
          ${createForecastSlot('18:00', 28)}
        `;
      }
    }
  };

  const initWeather = () => {
    if (document.querySelector('.weather-widget') || document.getElementById('current-temp')) {
      fetchWeather();
      setInterval(fetchWeather, UPDATE_INTERVAL);
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWeather);
  } else {
    initWeather();
  }

})();
