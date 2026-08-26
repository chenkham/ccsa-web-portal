/**
 * Weather Widget Module
 * Real-time weather for Dibrugarh University campus with Day/Night awareness & WMO code accuracy
 * 
 * @author Chenkham Chowlu
 * @copyright (c) 2024-2026 Chenkham Chowlu
 */
(() => {
  'use strict';

  const LAT = 27.4745;
  const LON = 94.9110;
  const WEATHER_URL = `https://api.open-meteo.com/v1/forecast?latitude=${LAT}&longitude=${LON}&current_weather=true&hourly=temperature_2m,is_day,weathercode&timezone=Asia/Kolkata&forecast_days=2`;
  const UPDATE_INTERVAL = 600000; // 10 minutes

  /**
   * Determine whether current local time is daytime (approx 6:00 AM to 6:00 PM in Assam)
   */
  const isLocalDaytime = (date = new Date()) => {
    const hour = date.getHours();
    return hour >= 6 && hour < 18;
  };

  /**
   * Maps WMO Weather Codes + Day/Night to accurate Emoji and Condition Text
   */
  const getWeatherDetails = (weatherCode, isDay = null, temp = 25) => {
    // If isDay is not explicitly provided from API, calculate from clock
    if (isDay === null || isDay === undefined) {
      isDay = isLocalDaytime() ? 1 : 0;
    }
    const day = Boolean(isDay);

    let icon = day ? '☀️' : '🌙';
    let label = day ? 'Clear Sky' : 'Clear Night';

    const code = Number(weatherCode);

    if (code === 0) {
      icon = day ? (temp > 32 ? '🌞' : '☀️') : '🌙';
      label = day ? 'Clear Sunshine' : 'Clear Starry Night';
    } else if (code === 1 || code === 2) {
      icon = day ? '🌤️' : '☁️';
      label = day ? 'Mainly Clear' : 'Partly Cloudy Night';
    } else if (code === 3) {
      icon = '☁️';
      label = 'Overcast';
    } else if (code === 45 || code === 48) {
      icon = '🌫️';
      label = 'Foggy / Mist';
    } else if (code >= 51 && code <= 55) {
      icon = day ? '🌦️' : '🌧️';
      label = 'Light Drizzle';
    } else if (code >= 56 && code <= 57) {
      icon = '🌧️';
      label = 'Freezing Drizzle';
    } else if (code >= 61 && code <= 65) {
      icon = '🌧️';
      label = code === 65 ? 'Heavy Rain' : 'Rain';
    } else if (code >= 80 && code <= 82) {
      icon = '🌧️';
      label = 'Rain Showers';
    } else if ((code >= 71 && code <= 77) || code === 85 || code === 86) {
      icon = '❄️';
      label = 'Cold / Snow Flurries';
    } else if (code >= 95 && code <= 99) {
      icon = '⛈️';
      label = 'Thunderstorm';
    } else {
      // Fallback by temp & day/night
      if (day) {
        icon = temp > 30 ? '☀️' : (temp > 22 ? '🌤️' : '⛅');
        label = 'Pleasant Day';
      } else {
        icon = '🌙';
        label = 'Clear Night';
      }
    }

    return { icon, label, isDay: day };
  };

  /**
   * Generates a descriptive, friendly campus note for the tooltip/title
   */
  const getCampusWeatherNote = (temp, details) => {
    const { isDay, label } = details;
    const rounded = Math.round(temp);

    if (details.icon === '⛈️') {
      return `Thunderstorm (${rounded}°C) in Dibrugarh · Stay indoors & stay safe ⚡`;
    }
    if (details.icon === '🌧️' || details.icon === '🌦️') {
      return `${label} (${rounded}°C) · Refreshing rain in Dibrugarh, perfect for hot Assam tea 🫖🌧️`;
    }
    if (!isDay) {
      if (rounded > 27) return `Warm night (${rounded}°C) in Dibrugarh · Perfect for late-night study & lab work 💻`;
      if (rounded > 20) return `Pleasant night (${rounded}°C) at Dibrugarh University · Peaceful campus hours 🌙`;
      return `Cool breeze (${rounded}°C) over Dibrugarh · Rest well 🌙`;
    } else {
      if (rounded > 32) return `Hot & sunny (${rounded}°C) in Dibrugarh · Stay hydrated 🌞`;
      if (rounded > 24) return `Warm & pleasant (${rounded}°C) · Great day for campus walks around DU 🌤️`;
      return `Mild & cool (${rounded}°C) at Dibrugarh University 🍃`;
    }
  };

  const showLoadingState = () => {
    const currentTemp = document.getElementById('current-temp');
    const currentIcon = document.getElementById('current-icon');
    const currentTime = document.getElementById('current-time');

    const now = new Date();
    const isDay = isLocalDaytime(now);

    if (currentTemp && currentTemp.textContent.trim() === '--°C') currentTemp.textContent = '...';
    if (currentIcon && (currentIcon.textContent.trim() === '' || currentIcon.textContent.trim() === '☀️')) {
      currentIcon.textContent = isDay ? '🌤️' : '🌙';
    }
    if (currentTime && currentTime.textContent.trim() === '--:--') {
      currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }
  };

  const createForecastSlot = (time, temp, isDay, weatherCode) => {
    const details = getWeatherDetails(weatherCode, isDay, temp);
    return `
      <div class="flex flex-col items-center justify-center p-1.5 sm:p-2 bg-slate-800/80 border border-slate-700/60 rounded-xl text-center shadow-xs transition-all hover:bg-slate-750" title="${time}: ${details.label} (${Math.round(temp)}°C)">
        <span class="text-[10px] font-bold text-slate-400 mb-0.5">${time}</span>
        <span class="text-base sm:text-lg my-0.5" role="img" aria-label="${details.label}">${details.icon}</span>
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

      const cw = data.current_weather;
      const temp = cw.temperature;
      const weatherCode = cw.weathercode;
      const isDay = cw.is_day;

      const details = getWeatherDetails(weatherCode, isDay, temp);

      if (currentTemp) {
        currentTemp.textContent = `${Math.round(temp)}°C`;
        const weatherNote = getCampusWeatherNote(temp, details);
        currentTemp.setAttribute('title', weatherNote);
      }
      if (currentIcon) {
        currentIcon.textContent = details.icon;
        currentIcon.setAttribute('title', details.label);
      }
      
      const now = new Date();
      if (currentTime) {
        currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
      }

      if (hourlyContainer && data.hourly && data.hourly.time && data.hourly.temperature_2m) {
        hourlyContainer.innerHTML = '';
        
        let startIndex = 0;
        const nowMs = now.getTime();
        for (let i = 0; i < data.hourly.time.length; i++) {
          const tDate = new Date(data.hourly.time[i]);
          if (tDate.getTime() >= nowMs) {
            startIndex = i;
            break;
          }
        }

        const hourlyIsDay = data.hourly.is_day || [];
        const hourlyCodes = data.hourly.weathercode || [];

        for (let i = 0; i < 4; i++) {
          const idx = startIndex + i;
          if (idx < data.hourly.time.length) {
            const timeStr = new Date(data.hourly.time[idx]).toLocaleTimeString('en-US', { hour: 'numeric', hour12: true });
            const fTemp = data.hourly.temperature_2m[idx];
            const fIsDay = hourlyIsDay[idx] !== undefined ? hourlyIsDay[idx] : (isLocalDaytime(new Date(data.hourly.time[idx])) ? 1 : 0);
            const fCode = hourlyCodes[idx] !== undefined ? hourlyCodes[idx] : 0;
            hourlyContainer.innerHTML += createForecastSlot(timeStr, fTemp, fIsDay, fCode);
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
      // Dynamic day/night aware offline fallback for Dibrugarh
      const now = new Date();
      const isDay = isLocalDaytime(now);
      const currentTemp = document.getElementById('current-temp');
      const currentIcon = document.getElementById('current-icon');
      const currentTime = document.getElementById('current-time');
      const hourlyContainer = document.getElementById('hourly-forecast');

      const fallbackTemp = isDay ? 30 : 25;
      const fallbackDetails = getWeatherDetails(0, isDay ? 1 : 0, fallbackTemp);

      if (currentTemp) {
        currentTemp.textContent = `${fallbackTemp}°C`;
        currentTemp.setAttribute('title', getCampusWeatherNote(fallbackTemp, fallbackDetails));
      }
      if (currentIcon) currentIcon.textContent = fallbackDetails.icon;
      if (currentTime) currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
      
      if (hourlyContainer) {
        hourlyContainer.innerHTML = '';
        const currentHour = now.getHours();
        for (let i = 1; i <= 4; i++) {
          const slotHour = (currentHour + i) % 24;
          const slotIsDay = (slotHour >= 6 && slotHour < 18) ? 1 : 0;
          const slotTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), slotHour).toLocaleTimeString('en-US', { hour: 'numeric', hour12: true });
          const slotTemp = slotIsDay ? 31 - i : 26 - i;
          hourlyContainer.innerHTML += createForecastSlot(slotTime, slotTemp, slotIsDay, 0);
        }
      }
    }
  };

  const initWeather = () => {
    if (document.querySelector('.weather-widget') || document.getElementById('current-temp')) {
      showLoadingState();
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
