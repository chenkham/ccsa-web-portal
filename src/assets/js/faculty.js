/**
 * Faculty Directory Rendering Module
 * Fetches data via the caching proxy and renders faculty profile cards
 */
(() => {
  'use strict';

  // Local fallback faculty list in case upstream university API is unavailable
  const fallbackFaculty = [
    {
      name: "Dr. Rizwan Rehman",
      designation: "Assistant Professor",
      qualification: "Ph.D., M.Tech, B.E.",
      specialization: "Cloud Computing, Big Data, Machine Learning",
      email: "rizwanrehman@dibru.ac.in",
      image: "faculty/Rizwan_SIr.png"
    },
    {
      name: "Dr. Utpola Borgohain",
      designation: "Assistant Professor",
      qualification: "Ph.D., MCA",
      specialization: "Data Mining, NLP, Machine Learning",
      email: "utpolaborgohain@dibru.ac.in",
      image: "faculty/UtpolaMam.png"
    },
    {
      name: "Dr. Ujjal Saikia",
      designation: "Assistant Professor",
      qualification: "Ph.D., MCA",
      specialization: "Image Processing, Computer Vision",
      email: "ujjalsaikia@dibru.ac.in",
      image: "faculty/UjjalSir.png"
    },
    {
      name: "Mr. Pranjal Kumar Bora",
      designation: "Assistant Professor",
      qualification: "M.Tech, MCA",
      specialization: "Wireless Sensor Networks, IoT",
      email: "pranjalbora@dibru.ac.in",
      image: "faculty/PranjalSir.png"
    },
    {
      name: "Ms. Gunajan Borah",
      designation: "Assistant Professor",
      qualification: "MCA",
      specialization: "Web Technologies, Information Security",
      email: "gunajanborah@dibru.ac.in",
      image: "faculty/Gunajn Sir.png"
    },
    {
      name: "Ms. Torali Gogoi",
      designation: "Assistant Professor",
      qualification: "M.Tech, MCA",
      specialization: "Artificial Intelligence, Data Analytics",
      email: "toraligogoi@dibru.ac.in",
      image: "faculty/ToraliMam.png"
    },
    {
      name: "Ms. Sumpi Moni Saikia",
      designation: "Assistant Professor",
      qualification: "MCA",
      specialization: "Software Engineering, Database Systems",
      email: "sumpisaikia@dibru.ac.in",
      image: "faculty/SumpiMam.png"
    },
    {
      name: "Mr. Ankumon Sarmah",
      designation: "Assistant Professor",
      qualification: "MCA",
      specialization: "Distributed Systems, Network Security",
      email: "ankumon@dibru.ac.in",
      image: "faculty/AnkumonSir.png"
    },
    {
      name: "Ms. Pinakshi Boruah",
      designation: "Assistant Professor",
      qualification: "MCA",
      specialization: "Programming Paradigms, Algorithms",
      email: "pinakshiboruah@dibru.ac.in",
      image: "faculty/PinakshiMam.png"
    },
    {
      name: "Ms. Kankan Barman",
      designation: "Assistant Professor",
      qualification: "MCA",
      specialization: "Operating Systems, Computer Networks",
      email: "kankanbarman@dibru.ac.in",
      image: "faculty/KanKanMam.png"
    },
    {
      name: "Ms. Kimasha Borah",
      designation: "Assistant Professor",
      qualification: "MCA",
      specialization: "Cyber Security, Data Science",
      email: "kimashaborah@dibru.ac.in",
      image: "faculty/KimashaMam.png"
    }
  ];

  /**
   * Helper to find local image match by faculty name
   */
  const getFacultyImage = (name, apiImage) => {
    if (apiImage && apiImage.startsWith('http')) return apiImage;
    const cleanName = (name || '').toLowerCase();
    if (cleanName.includes('rizwan')) return 'faculty/Rizwan_SIr.png';
    if (cleanName.includes('utpola')) return 'faculty/UtpolaMam.png';
    if (cleanName.includes('ujjal')) return 'faculty/UjjalSir.png';
    if (cleanName.includes('pranjal')) return 'faculty/PranjalSir.png';
    if (cleanName.includes('gunajan') || cleanName.includes('gunajn')) return 'faculty/Gunajn Sir.png';
    if (cleanName.includes('torali')) return 'faculty/ToraliMam.png';
    if (cleanName.includes('sumpi')) return 'faculty/SumpiMam.png';
    if (cleanName.includes('ankumon')) return 'faculty/AnkumonSir.png';
    if (cleanName.includes('pinakshi')) return 'faculty/PinakshiMam.png';
    if (cleanName.includes('kankan') || cleanName.includes('kan kan')) return 'faculty/KanKanMam.png';
    if (cleanName.includes('kimasha')) return 'faculty/KimashaMam.png';
    if (cleanName.includes('deka') || cleanName.includes('paramananda')) return 'faculty/Chairperson.jpg';
    return 'faculty/du.png';
  };

  /**
   * Render faculty list to DOM with Reference Design Styling
   */
  const renderFaculty = (facultyList) => {
    const container = document.getElementById('students-container');
    if (!container) return;

    container.innerHTML = '';

    facultyList.forEach(member => {
      const card = document.createElement('div');
      card.className = 'faculty-card-ref flex flex-col sm:flex-row items-center sm:items-start gap-5 p-5 sm:p-6 bg-white rounded-2xl shadow-sm border border-slate-200/80 hover:shadow-md transition-all duration-300';

      const name = member.full_name || member.name || 'Faculty Member';
      const designation = member.designation || member.position || 'Assistant Professor';
      const qualification = member.qualification || '';
      const specialization = member.fp_specializations || member.specialization || member.department || '';
      const email = member.email || '';
      const imgSrc = member.photo || getFacultyImage(name, member.image || member.profile_image);

      // Generate official Dibrugarh University profile URL using slug or normalized name
      const slug = member.slug || name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
      const profileUrl = `https://www.dibru.ac.in/teachers-profile/${slug}`;

      card.innerHTML = `
        <!-- Custom Academic Crest Silhouette Frame -->
        <div class="faculty-crest-frame w-32 h-36 sm:w-36 sm:h-40 flex-shrink-0 overflow-hidden bg-slate-100 border border-slate-200 flex items-end justify-center shadow-inner rounded-xl">
          <img src="${imgSrc}" alt="${name}" class="w-full h-full object-cover object-top transition-transform duration-500" onerror="this.src='faculty/du.png'">
        </div>

        <!-- Faculty Details (Reference Styling) -->
        <div class="flex-1 text-center sm:text-left flex flex-col justify-between self-stretch">
          <div>
            <h3 class="text-lg sm:text-xl font-bold font-display text-[#d97706] uppercase tracking-wide">
              ${name}
            </h3>
            <div class="w-16 h-0.5 bg-slate-300 mt-1 mb-2 mx-auto sm:mx-0"></div>

            <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
              ${designation}
            </p>
            ${qualification ? `<p class="text-xs text-slate-500 mt-0.5">${qualification}</p>` : ''}
            ${specialization ? `<p class="text-xs text-indigo-700 font-medium mt-1 leading-snug"><i class="fas fa-microscope mr-1 text-indigo-500"></i> ${specialization}</p>` : ''}
          </div>

          <div class="mt-4 pt-2 flex items-center justify-center sm:justify-start gap-3">
            <a href="${profileUrl}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800 hover:text-amber-600 transition-colors uppercase tracking-wider group/link" title="View official Dibrugarh University profile for ${name}">
              <span>VIEW PROFILE</span>
              <span class="w-5 h-5 bg-[#fbbf24] text-slate-900 flex items-center justify-center rounded-sm text-[10px] font-black group-hover/link:bg-amber-400 transition-colors">&gt;</span>
            </a>
          </div>
        </div>
      `;

      container.appendChild(card);
    });
  };

  /**
   * Display faculty data from caching proxy or fallback
   */
  window.displayFaculty = async () => {
    const container = document.getElementById('students-container');
    if (!container) return;

    // Show skeletons while loading
    container.innerHTML = Array(6).fill(0).map(() => `
      <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 flex flex-col sm:flex-row items-center gap-4 animate-pulse">
        <div class="w-32 h-36 rounded-xl bg-slate-200 flex-shrink-0"></div>
        <div class="flex-1 space-y-3 w-full">
          <div class="h-5 bg-slate-200 rounded w-3/4"></div>
          <div class="h-4 bg-slate-200 rounded w-1/2"></div>
          <div class="h-3 bg-slate-200 rounded w-5/6"></div>
        </div>
      </div>
    `).join('');

    try {
      const response = await fetch('proxy/faculty.php');
      if (!response.ok) throw new Error(`Proxy error: ${response.status}`);
      
      const data = await response.json();
      let list = [];

      if (Array.isArray(data)) {
        data.forEach(group => {
          // Skip Chairperson group to only include teaching staff
          if (group.name && group.name.toLowerCase().includes('chairperson')) {
            return;
          }
          if (group.members && Array.isArray(group.members)) {
            const teachingMembers = group.members.filter(m => {
              const name = (m.name || m.full_name || '').toLowerCase();
              const slug = (m.slug || '').toLowerCase();
              return !name.includes('paramananda') && !slug.includes('paramananda');
            });
            list.push(...teachingMembers);
          } else if (group.name || group.full_name) {
            const name = (group.name || group.full_name || '').toLowerCase();
            if (!name.includes('paramananda')) {
              list.push(group);
            }
          }
        });
      } else if (data && data.data && Array.isArray(data.data)) {
        list = data.data.filter(m => {
          const name = (m.name || m.full_name || '').toLowerCase();
          return !name.includes('paramananda');
        });
      }

      if (list.length === 0) {
        list = fallbackFaculty;
      }

      renderFaculty(list);
    } catch (err) {
      console.warn('Faculty proxy fetch failed, rendering fallback data:', err);
      renderFaculty(fallbackFaculty);
    }

  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof window.displayFaculty === 'function') {
        window.displayFaculty();
      }
    });
  } else {
    window.displayFaculty();
  }
})();

