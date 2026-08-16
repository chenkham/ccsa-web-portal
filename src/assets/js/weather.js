/**
 * Weather Widget Module
 */
(() => {
  'use strict';

  const LAT = 27.4745;
  const LON = 94.9110;
  const WEATHER_URL = `https://api.open-meteo.com/v1/forecast?latitude=${LAT}&longitude=${LON}&current_weather=true&hourly=temperature_2m&timezone=Asia/Kolkata&forecast_days=1`;
  const UPDATE_INTERVAL = 900000; // 15 minutes in ms

  /**
   * Returns emoji based on temperature range
   * @param {number} temp Temperature in Celsius
   * @returns {string} Emoji string
   */
  const getWeatherIcon = (temp) => {
    if (temp > 30) return '🌞';
    if (temp > 25) return '☀️';
    if (temp > 20) return '⛅';
    if (temp > 15) return '☁️';
    if (temp > 10) return '🌧️';
    return '⛈️';
  };

  const showLoadingState = () => {
    const currentTemp = document.getElementById('current-temp');
    const currentIcon = document.getElementById('current-icon');
    const currentTime = document.getElementById('current-time');
    const hourlyContainer = document.getElementById('hourly-forecast');

    if (currentTemp) currentTemp.textContent = '--°C';
    if (currentIcon) currentIcon.textContent = '⏳';
    if (currentTime) currentTime.textContent = 'Loading...';
    
    if (hourlyContainer) {
      hourlyContainer.innerHTML = '';
      for (let i = 0; i < 4; i++) {
        const skeleton = document.createElement('div');
        skeleton.className = 'w-12 h-16 rounded-md skeleton flex-shrink-0';
        hourlyContainer.appendChild(skeleton);
      }
    }
  };

  const showErrorState = () => {
    const currentTemp = document.getElementById('current-temp');
    const currentIcon = document.getElementById('current-icon');
    const currentTime = document.getElementById('current-time');
    const hourlyContainer = document.getElementById('hourly-forecast');

    if (currentTemp) currentTemp.textContent = '--°C';
    if (currentIcon) currentIcon.textContent = '❌';
    if (currentTime) currentTime.textContent = 'Error loading weather';
    if (hourlyContainer) hourlyContainer.innerHTML = '<p class="text-xs text-red-500 w-full text-center">Failed to fetch forecast</p>';
  };

  /**
   * Creates HTML for one hourly forecast slot
   * @param {string} time Time string (e.g., "14:00")
   * @param {number} temp Temperature
   * @returns {string} HTML string
   */
  const createForecastSlot = (time, temp) => {
    return `
      <div class="flex flex-col items-center justify-center p-2 bg-slate-50 rounded-lg min-w-[3rem]">
        <span class="text-xs font-semibold text-slate-500 mb-1">${time}</span>
        <span class="text-lg mb-1">${getWeatherIcon(temp)}</span>
        <span class="text-sm font-bold text-slate-700">${Math.round(temp)}°</span>
      </div>
    `;
  };

  const updateWeatherDisplay = (data) => {
    try {
      const currentTemp = document.getElementById('current-temp');
      const currentIcon = document.getElementById('current-icon');
      const currentTime = document.getElementById('current-time');
      const hourlyContainer = document.getElementById('hourly-forecast');

      if (!data || !data.current_weather) throw new Error('Invalid data');

      const temp = data.current_weather.temperature;
      
      if (currentTemp) currentTemp.textContent = `${Math.round(temp)}°C`;
      if (currentIcon) currentIcon.textContent = getWeatherIcon(temp);
      
      const now = new Date();
      if (currentTime) {
        currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
      }

      if (hourlyContainer && data.hourly && data.hourly.time && data.hourly.temperature_2m) {
        hourlyContainer.innerHTML = '';
        
        // Find current hour index
        const currentHourIso = now.toISOString().slice(0, 14) + '00';
        // Open-Meteo times are like "2023-05-15T12:00"
        
        // Simple approach: get the next 4 available forecast hours from current time
        const currentHourStr = now.getHours();
        let startIndex = currentHourStr; // assuming the data arrays match the hours of the day
        
        // Fallback robust search
        for(let i=0; i<data.hourly.time.length; i++) {
          const tDate = new Date(data.hourly.time[i]);
          if(tDate.getTime() >= now.getTime()) {
            startIndex = i;
            break;
          }
        }

        for (let i = 0; i < 4; i++) {
          const index = startIndex + i;
          if (index < data.hourly.time.length) {
            const timeStr = new Date(data.hourly.time[index]).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
            const fTemp = data.hourly.temperature_2m[index];
            hourlyContainer.innerHTML += createForecastSlot(timeStr, fTemp);
          }
        }
      }
    } catch (error) {
      console.error('Error updating weather display:', error);
      showErrorState();
    }
  };

  const fetchWeather = async () => {
    try {
      showLoadingState();
      const response = await fetch(WEATHER_URL);
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      const data = await response.json();
      updateWeatherDisplay(data);
    } catch (error) {
      console.error('Failed to fetch weather:', error);
      showErrorState();
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if widget exists
    if (document.querySelector('.weather-widget') || document.getElementById('current-temp')) {
      fetchWeather();
      setInterval(fetchWeather, UPDATE_INTERVAL);
    }
  });

})();
