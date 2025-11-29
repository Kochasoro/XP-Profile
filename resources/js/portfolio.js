let portfoliosData = [];
let usersData = [];
let skillsData = [];
let experienceData = [];
let educationData = [];
let projectsData = [];

document.addEventListener("DOMContentLoaded", loadPortfolios);

function loadPortfolios() {
    Promise.all([
        fetch("api/Portfolio_api.php"),
        fetch("api/User_api.php"),
        fetch("api/Skills_api.php"),
        fetch("api/Experience_api.php"),
        fetch("api/Education_api.php"),
        fetch("api/Projects_api.php") 
    ])
    .then(responses => Promise.all(responses.map(res => res.json())))
    .then(([portfolioData, usersDataResponse, skillsDataResponse, experienceDataResponse, educationDataResponse, projectsDataResponse]) => {
        try {
            const { user_id, portfolios } = portfolioData;
            const users = usersDataResponse.users || [];
            const skills = skillsDataResponse.skills || [];
            const experiences = experienceDataResponse.experiences || [];
            const education = educationDataResponse.education || [];
            const projects = projectsDataResponse.projects || [];

            usersData = users;
            portfoliosData = portfolios;
            skillsData = skills;
            experienceData = experiences;
            educationData = education;
            projectsData = projects;

            displayPortfolios(portfolios, user_id);  // Your render logic
        } catch (e) {
            console.error("Error processing data:", e);
        }
    })
    .catch(error => console.error("Error fetching data:", error));
}

console.log("Logged in User ID:", loggedInUserId);

let editButton = '';


function displayPortfolios(portfoliosData, loggedInUserId) {
    const portfoliosTableBody = document.getElementById("portfoliosTableBody");
    portfoliosTableBody.innerHTML = "";

    portfoliosData.forEach((portfolio, index) => {
        const profileImageSrc = portfolio.profile_image && portfolio.profile_image !== "null"
            ? `api/${portfolio.profile_image}`
            : 'resources/images/default.jpg';

        const isOwner = portfolio.user_id === loggedInUserId;

        const editButtons = isOwner ? `
            <div class="portfolio-actions">
                <button onclick="openModal(${portfolio.id}, '${portfolio.title.replace(/'/g, "\\'")}', '${portfolio.summary.replace(/'/g, "\\'")}', '${profileImageSrc}')">Edit</button>
                <button onclick="deletePortfolio(${portfolio.id})">Delete</button>
            </div>
        ` : '';

        const itemDiv = document.createElement("div");
        itemDiv.className = "portfolio-item";
        itemDiv.draggable = true;

        // Set initial layout mode to relative to align items like desktop icons
        itemDiv.style.position = "relative";

        itemDiv.innerHTML = `
            <img src="${profileImageSrc}" alt="${portfolio.title}" onclick="viewPortfolioDetails(${portfolio.id})">
            <a href="#" onclick="viewPortfolioDetails(${portfolio.id})">
                ${portfolio.title.length > 15 ? portfolio.title.substring(0, 15) + '...' : portfolio.title}
            </a>
            <div style="font-size: 12px; color: #ccc;">
                ${portfolio.summary.length > 15 ? portfolio.summary.substring(0, 15) + '...' : portfolio.summary}
            </div>
            ${editButtons}
        `;

        // Prevent native drag behavior for children
        itemDiv.querySelectorAll('img, a, button').forEach(el => {
            el.addEventListener('dragstart', (e) => e.preventDefault());
        });

        // DRAG EVENTS
        itemDiv.addEventListener('dragstart', (e) => {
            itemDiv.classList.add('dragging');

            const rect = itemDiv.getBoundingClientRect();
            const parentRect = portfoliosTableBody.getBoundingClientRect();

            currentDragData = {
                offsetX: e.clientX - rect.left,
                offsetY: e.clientY - rect.top,
                parentLeft: parentRect.left,
                parentTop: parentRect.top
            };
        });

        itemDiv.addEventListener('dragend', (e) => {
            itemDiv.classList.remove('dragging');
            if (!currentDragData) return;

            // Switch to absolute to allow free positioning when dragging
            itemDiv.style.position = 'absolute';

            // Calculate the position after drag
            const x = e.clientX - currentDragData.parentLeft - currentDragData.offsetX;
            const y = e.clientY - currentDragData.parentTop - currentDragData.offsetY;

            itemDiv.style.left = `${x}px`;
            itemDiv.style.top = `${y}px`;

            currentDragData = null;
        });

        portfoliosTableBody.appendChild(itemDiv);
    });

    portfoliosTableBody.addEventListener('dragover', (e) => e.preventDefault());
}


function decodeHtmlEntities(str) {
    const txt = document.createElement("textarea");
    txt.innerHTML = str;
    return txt.value;
}

function openModal(id = "", title = "", summary = "", profile_image = null) {
    title = decodeHtmlEntities(title);
    summary = decodeHtmlEntities(summary);

    document.getElementById("portfolioId").value = id;
    document.getElementById("title").value = title;
    document.getElementById("summary").value = summary;

    const profileImagePreview = document.getElementById("profileImagePreview");
    const deleteImageBtn = document.getElementById("deleteImageBtn");

    if (!profile_image || profile_image === "api/null") {
        profileImagePreview.src = "resources/images/default.jpg";  
        profileImagePreview.style.display = "block"; 
        deleteImageBtn.disabled = true;  
        deleteImageBtn.style.display = "none";  
    } else {
        profileImagePreview.src = profile_image;
        profileImagePreview.style.display = "block";
        deleteImageBtn.disabled = false;  
        deleteImageBtn.style.display = "none"; 
    }

    if (id) {
        document.getElementById("modalTitle").innerText = "Edit Portfolio";
        document.getElementById("saveUpdateBtn").innerText = "Update Portfolio";
        document.getElementById("saveUpdateBtn").setAttribute("onclick", "updatePortfolio()");
    } else {
        document.getElementById("modalTitle").innerText = "Add Portfolio";
        document.getElementById("saveUpdateBtn").innerText = "Save Portfolio";
        document.getElementById("saveUpdateBtn").setAttribute("onclick", "savePortfolio()");
    }

    document.getElementById("portfolioModal").style.display = "block";
}


function deleteImage() {
    const profileImageInput = document.getElementById("profile_image");
    const profileImagePreview = document.getElementById("profileImagePreview");
    const deleteImageBtn = document.getElementById("deleteImageBtn");

    profileImageInput.value = "";  
    profileImagePreview.src = "resources/images/default.jpg"; 
    profileImagePreview.style.display = "block"; 
    deleteImageBtn.style.display = "none";  

    const id = document.getElementById("portfolioId").value;
    const title = document.getElementById("title").value;
    const summary = document.getElementById("summary").value;

    sendPutRequest(id, title, summary, null);  
}

document.addEventListener("DOMContentLoaded", function () {
    const profileImageWrapper = document.getElementById("profileImageWrapper");
    const deleteImageBtn = document.getElementById("deleteImageBtn");
    const profileImagePreview = document.getElementById("profileImagePreview");

    profileImageWrapper.addEventListener('mouseenter', () => {
        if (!deleteImageBtn.disabled) {  
            deleteImageBtn.style.display = 'block';
        }
    });

    profileImageWrapper.addEventListener('mouseleave', () => {
        if (!deleteImageBtn.disabled) { 
            deleteImageBtn.style.display = 'none';
        }
    });

    deleteImageBtn.addEventListener('click', () => {
        const confirmDelete = confirm("Are you sure you want to delete the image?");
        if (confirmDelete) {
            deleteImage();
        }
    });

    loadPortfolios();
});


function closeModal() {
    document.getElementById("portfolioModal").style.display = "none";
}

function savePortfolio() {
    let title = document.getElementById("title").value;
    let summary = document.getElementById("summary").value;
    let profileImage = document.getElementById("profile_image").files[0];

    if (!title || !summary) {
        alert("Title and summary are required.");
        return;
    }

    let formData = new FormData();
    formData.append("title", title);
    formData.append("summary", summary);
    formData.append("profile_image", profileImage);

    fetch("api/Portfolio_api.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Portfolio added successfully");
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Error saving portfolio:", error);
    });
}



function updatePortfolio() {
    let id = document.getElementById("portfolioId").value;
    let title = document.getElementById("title").value;
    let summary = document.getElementById("summary").value;
    let profileInput = document.getElementById("profile_image");
    let file = profileInput.files[0];

    if (file) {
        let reader = new FileReader();
        reader.onloadend = function () {
            let base64Image = reader.result; 

            sendPutRequest(id, title, summary, base64Image);
        };
        reader.readAsDataURL(file);
    } else {
        sendPutRequest(id, title, summary, null);
    }
}

function sendPutRequest(id, title, summary, base64Image) {
    fetch("api/Portfolio_api.php", {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            id: id,
            title: title,
            summary: summary,
            profile_image: base64Image
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        closeModal();
        loadPortfolios();
    })
    .catch(error => console.error("Error updating portfolio:", error));
}

function deletePortfolio(id) {
    if (!confirm("Are you sure you want to delete this portfolio?")) return;

    fetch("api/Portfolio_api.php", {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadPortfolios();
    })
    .catch(error => console.error("Error deleting portfolio:", error));
}

function searchPortfolios() {
    let searchTerm = document.getElementById("searchInput").value.toLowerCase();
    let filteredPortfolios = portfoliosData.filter(portfolio => 
        portfolio.title.toLowerCase().includes(searchTerm) || 
        portfolio.summary.toLowerCase().includes(searchTerm)
    );
    displayPortfolios(filteredPortfolios, loggedInUserId);
}



// WINDOWS

function viewPortfolioDetails(portfolioId) {
    closeAllPopupsWithAnimation(() => {
        currentPortfolioId = portfolioId;
        const portfolio = portfoliosData.find(p => p.id === portfolioId);

        if (portfolio) {
            const detailTitleElement = document.getElementById("detailTitle");
            const profileImageElement = document.getElementById("popupProfileImage");
            const portfolioDetailsPopup = document.getElementById("portfolioDetailsPopup");
            const nameElement = document.getElementById("popupName");
            const mobileElement = document.getElementById("popupMobile");

            if (detailTitleElement && profileImageElement && nameElement && portfolioDetailsPopup) {
                detailTitleElement.innerText = portfolio.title;
                profileImageElement.src = `api/${portfolio.profile_image}`;
                const profileImageSrc = portfolio.profile_image && portfolio.profile_image !== "null"
                    ? `api/${portfolio.profile_image}`
                    : 'resources/images/default.jpg';

                profileImageElement.src = profileImageSrc;
                const owner = usersData.find(user => user.id == portfolio.user_id);
                
                if (owner) {
                    const fullName = `${owner.firstname} ${owner.middlename} ${owner.lastname}`.replace(/\s+/g, ' ').trim();
                    nameElement.innerHTML = `<a href="portfolio_details?id=${portfolio.id}" style="color: inherit; text-decoration: none; cursor: pointer;">${fullName}</a>`;

                    if (mobileElement) {
                        mobileElement.innerText = owner.mobile;
                    }
                } else {
                    nameElement.innerHTML = `<a href="portfolio_details?id=${portfolio.id}" style="color: inherit; text-decoration: none; cursor: pointer;">Unknown User</a>`;
                    if (mobileElement) {
                        mobileElement.innerText = "N/A";
                    }
                }

                portfolioDetailsPopup.style.display = "block";
                portfolioDetailsPopup.classList.add("popup-zoom-in");

                setTimeout(() => {
                    portfolioDetailsPopup.classList.remove("popup-zoom-in");
                }, 300);
            } else {
                console.error("One or more elements not found.");
            }
        }

        console.log("Portfolio ID:", currentPortfolioId);
    });
}


function closeAllPopupsWithAnimation(callback) {
    const subcategoryPopups = document.querySelectorAll(".subcategory-popup");
    const mainPopup = document.querySelectorAll(".popup");

    subcategoryPopups.forEach(popup => {
        popup.classList.add("popup-zoom-out");
        setTimeout(() => {
            popup.classList.remove("popup-zoom-out");
            popup.remove(); 
        }, 300);
    });

    mainPopup.forEach(popup => {
        popup.classList.add("popup-zoom-out");
        setTimeout(() => {
            popup.style.display = "none"; 
            popup.classList.remove("popup-zoom-out");
        }, 300);
    });

    setTimeout(callback, 300);
}



function openPopupWithAnimation(popupElement) {
    popupElement.style.display = "block";
    popupElement.classList.add("popup-zoom-in");

    setTimeout(() => {
        popupElement.classList.remove("popup-zoom-in");
    }, 300);
}




let currentPortfolioId = null;



function closePopup() {
    const popup = document.getElementById("portfolioDetailsPopup");

    if (popup) {
        popup.classList.add("popup-zoom-out");
        setTimeout(() => {
            popup.classList.remove("popup-zoom-out");
            popup.style.display = "none";
        }, 300);
    }

    const subcategoryPopups = document.querySelectorAll(".subcategory-popup");
    subcategoryPopups.forEach(subPopup => {
        subPopup.classList.add("popup-zoom-out");
        setTimeout(() => {
            subPopup.classList.remove("popup-zoom-out");
            subPopup.remove(); 
        }, 300);
    });
}

dragElement(document.getElementById("portfolioDetailsPopup"));

function dragElement(elmnt) {
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    const header = elmnt.getElementsByClassName("popup-header")[0];

    if (header) {
        header.onmousedown = dragMouseDown;
    } else {
        elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.onmousemove = null;
    }
}



//subs
function openSubcategoryWindow(subcategory, portfolioId) {
    const existingPopup = document.querySelector(`.subcategory-popup[data-subcategory="${subcategory}"]`);
    if (existingPopup) {
        return;
    }

    const newPopup = document.createElement("div");
    newPopup.classList.add("subcategory-popup");
    newPopup.style.display = "block";
    document.body.appendChild(newPopup);
    requestAnimationFrame(() => {
        newPopup.classList.add("popup-zoom-in");

        setTimeout(() => {
            newPopup.classList.remove("popup-zoom-in");
        }, 100);
    });

    const offsetX = Math.floor(Math.random() * 100) - 50; 
    const offsetY = Math.floor(Math.random() * 100) - 50;

    newPopup.style.position = "fixed";
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    const popupWidth = 700;
    const popupHeight = 300;

    const centerX = viewportWidth / 2 - popupWidth / 2 + offsetX;
    const centerY = viewportHeight / 2 - popupHeight / 2 + offsetY;

    newPopup.style.top = `${centerY}px`;
    newPopup.style.left = `${centerX}px`;
    newPopup.setAttribute("data-subcategory", subcategory);

    const header = document.createElement("div");
    header.classList.add("popup-header");
    header.innerHTML = `
        <span class="popup-close" onclick="closePopupSubcategory(this)">×</span>
        <h2>${subcategory}</h2>
    `;
    newPopup.appendChild(header);

    const content = document.createElement("div");
    content.classList.add("content");

    const portfolio = portfoliosData.find(p => p.id === portfolioId);
    if (portfolio) {
        if (subcategory === "Education") {
            const educations = educationData || [];

            const list = document.createElement("ul");
            list.id = `educationList-${portfolio.id}`; 

            if (educations.filter(edu => edu.portfolio_id === portfolio.id).length === 0) {
                const li = document.createElement("li");
                li.textContent = "No education details available.";
                list.appendChild(li);
            } else {
                educations.forEach((edu) => {
                    if (edu.portfolio_id === portfolio.id) {
                        const li = document.createElement("li");
                        li.id = `educationItem-${edu.id}`; 

                        if (portfolio.user_id === loggedInUserId) {
                            li.innerHTML = `
                                <a href="#" onclick="editEducation(${edu.id}, '${edu.degree.replace(/'/g, "\\'")}', '${edu.school_name.replace(/'/g, "\\'")}', '${edu.start_year}', '${edu.end_year}', '${edu.description.replace(/'/g, "\\'")}', ${portfolio.id})">
                                    <strong>${edu.degree}</strong> at ${edu.school_name}
                                </a> (${edu.start_year}–${edu.end_year})<br>${edu.description}
                                <button onclick="deleteEducation(${edu.id})">Delete</button>
                            `;
                        } else {
                            li.innerHTML = `
                                <strong>${edu.degree}</strong> at ${edu.school_name} (${edu.start_year}–${edu.end_year})<br>${edu.description}
                            `;
                        }

                        list.appendChild(li);
                    }
                });
            }
            content.appendChild(list);

            if (portfolio.user_id === loggedInUserId) {
                const actions = document.createElement("div");
                actions.classList.add("popup-actions");

                const editEduBtn = document.createElement("button");
                const addEduBtn = document.createElement("button");
                addEduBtn.innerText = "Add";
                addEduBtn.onclick = () => { 
                    const form = document.createElement("div");
                    form.innerHTML = `
                        <input type="text" placeholder="Degree" id="eduDegree" required>
                        <input type="text" placeholder="School" id="eduSchool" required>
                        <input type="text" placeholder="Start Year" id="eduStartYear" required>
                        <input type="text" placeholder="End Year" id="eduEndYear" required>
                        <textarea placeholder="Description" id="eduDescription"></textarea>
                        <button id="addEduBtn">Add Education</button>
                        <button id="cancelEduBtn">Dismiss</button>
                    `;                
                    actions.appendChild(form);
                
                    document.getElementById("addEduBtn").onclick = () => {
                        const degree = document.getElementById("eduDegree").value.trim();
                        const school = document.getElementById("eduSchool").value.trim();
                        const startYear = document.getElementById("eduStartYear").value.trim();
                        const endYear = document.getElementById("eduEndYear").value.trim();
                        const description = document.getElementById("eduDescription").value.trim();
                    
                        if (degree && school && startYear && endYear) {
                            saveNewEducationEntry(portfolio.id, {
                                degree, school, startYear, endYear, description
                            }).then(saved => {
                                if (saved) {
                                    const noEduc = list.querySelector("li");
                                    if (noEduc && noEduc.textContent.trim() === "No education details available.") {
                                        noEduc.remove();
                                    }
                                    const li = document.createElement("li");
                                    li.innerHTML = `<strong>${degree}</strong> at ${school} (${startYear}–${endYear})<br>${description}`;
                                    list.appendChild(li);
                                    form.remove();
                                } else {
                                    alert("Failed to save education.");
                                }
                            });
                        } else {
                            alert("All fields except description are required.");
                        }
                    };
                    
                    document.getElementById("cancelEduBtn").onclick = () => form.remove();
                    
                };
                

                actions.appendChild(addEduBtn);
                content.appendChild(actions);
            }
        } else if (subcategory === "Experience") {
            const experiences = experienceData || [];
        
            const list = document.createElement("ul");
            list.id = `experienceList-${portfolio.id}`;
        
            const filteredExperiences = experiences.filter(exp => exp.portfolio_id === portfolio.id);
            if (filteredExperiences.length === 0) {
                const li = document.createElement("li");
                li.textContent = "No experience details available.";
                list.appendChild(li);
            } else {
                filteredExperiences.forEach((exp) => {
                    const li = document.createElement("li");
                    li.id = `experienceItem-${exp.id}`;
            
                    if (portfolio.user_id === loggedInUserId) {
                        li.innerHTML = `
                            <a href="#" onclick="editExperience(${exp.id}, '${exp.job_title.replace(/'/g, "\\'")}', '${exp.company_name.replace(/'/g, "\\'")}', '${exp.start_date}', '${exp.end_date}', '${exp.description.replace(/'/g, "\\'")}', ${portfolio.id})">
                                <strong>${exp.job_title}</strong> at ${exp.company_name}
                            </a> (${exp.start_date}–${exp.end_date})<br>${exp.description}
                            <button onclick="deleteExperience(${exp.id})">Delete</button>
                        `;
                    } else {
                        li.innerHTML = `
                            <strong>${exp.job_title}</strong> at ${exp.company_name} (${exp.start_date}–${exp.end_date})<br>${exp.description}
                        `;
                    }
            
                    list.appendChild(li);
                });
            }
            
            content.appendChild(list);
            if (portfolio.user_id === loggedInUserId) {
                const actions = document.createElement("div");
                actions.classList.add("popup-actions");
            
                const addExpBtn = document.createElement("button");
                addExpBtn.innerText = "Add";
                addExpBtn.onclick = () => {
                    const form = document.createElement("div");
                    form.innerHTML = `
                        <input type="text" placeholder="Job Title" id="expJobTitle" required>
                        <input type="text" placeholder="Company Name" id="expCompany" required>
                        <input type="date" placeholder="Start Date" id="expStart" required>
                        <input type="date" placeholder="End Date" id="expEnd" required>
                        <textarea placeholder="Description" id="expDesc"></textarea>
                        <button id="addExpSubmit">Add Experience</button>
                        <button id="cancelExpBtn">Dismiss</button>
                    `;
                    actions.appendChild(form);
            
                    document.getElementById("addExpSubmit").onclick = () => {
                        const job_title = document.getElementById("expJobTitle").value.trim();
                        const company_name = document.getElementById("expCompany").value.trim();
                        const start_date = document.getElementById("expStart").value;
                        const end_date = document.getElementById("expEnd").value;
                        const description = document.getElementById("expDesc").value.trim();
            
                        if (job_title && company_name && start_date && end_date) {
                            saveNewExperienceEntry(portfolio.id, {
                                job_title, company_name, start_date, end_date, description
                            }).then(saved => {
                                if (saved) {
                                    const noExp = list.querySelector("li");
                                    if (noExp && noExp.textContent.trim() === "No experience details available.") {
                                        noExp.remove();
                                    }
                                    const li = document.createElement("li");
                                    li.innerHTML = `<strong>${job_title}</strong> at ${company_name} (${start_date}–${end_date})<br>${description}`;
                                    list.appendChild(li);
                                    form.remove();
                                } else {
                                    alert("Failed to save experience.");
                                }
                            });
                        } else {
                            alert("All fields except description are required.");
                        }
                    };
            
                    document.getElementById("cancelExpBtn").onclick = () => form.remove();
                };
            
                actions.appendChild(addExpBtn);
                content.appendChild(actions);
            }
            
        } else if (subcategory === "Skills") {
            const skills = skillsData || [];
        
            const list = document.createElement("ul");
            list.id = `skillsList-${portfolio.id}`;
        
            const filteredSkills = skills.filter(skill => skill.portfolio_id === portfolio.id);
            if (filteredSkills.length === 0) {
                const li = document.createElement("li");
                li.textContent = "No skills listed.";
                list.appendChild(li);
            } else {
                filteredSkills.forEach((skill) => {
                    const li = document.createElement("li");
                    li.id = `skillItem-${skill.id}`;
            
                    if (portfolio.user_id === loggedInUserId) {
                        li.innerHTML = `
                            <a href="#" onclick="editSkill(${skill.id}, '${skill.skill_name.replace(/'/g, "\\'")}', ${skill.proficiency}, ${portfolio.id})">
                                <strong>${skill.skill_name}</strong>
                            </a> - Level: ${skill.proficiency }
                            <button onclick="deleteSkill(${skill.id})">Delete</button>
                        `;
                    } else {
                        li.innerHTML = `
                            <strong>${skill.skill_name}</strong> - Level: ${skill.proficiency }
                        `;
                    }
            
                    list.appendChild(li);
                });
            }            
            content.appendChild(list);
            if (portfolio.user_id === loggedInUserId) {
                const actions = document.createElement("div");
                actions.classList.add("popup-actions");
            
                const addSkillBtn = document.createElement("button");
                addSkillBtn.innerText = "Add";
                addSkillBtn.onclick = () => {
                    const form = document.createElement("div");
                    form.innerHTML = `
                        <input type="text" placeholder="Skill Name" id="skillName" required>
                        <input type="number" placeholder="Proficiency (0-100)" id="skillProficiency" min="0" max="100" required>
                        <button id="addSkillSubmit">Add Skill</button>
                        <button id="cancelSkillBtn">Dismiss</button>
                    `;
                    actions.appendChild(form);
            
                    document.getElementById("addSkillSubmit").onclick = () => {
                        const skill_name = document.getElementById("skillName").value.trim();
                        const proficiency = parseInt(document.getElementById("skillProficiency").value.trim());
            
                        if (skill_name && proficiency) {
                            saveNewSkillEntry(portfolio.id, { skill_name, proficiency }).then(saved => {
                                if (saved) {
                                    const noSkillsItem = list.querySelector("li");
                                    if (noSkillsItem && noSkillsItem.textContent.trim() === "No skills listed.") {
                                        noSkillsItem.remove();
                                    }
                                    const li = document.createElement("li");
                                    li.innerHTML = `<strong>${skill_name}</strong> - Level: ${proficiency}`;
                                    list.appendChild(li);
                                    form.remove();
                                } else {
                                    alert("Failed to save skill.");
                                }
                            });
                        } else {
                            alert("All fields are required.");
                        }
                    };
            
                    document.getElementById("cancelSkillBtn").onclick = () => form.remove();
                };
            
                actions.appendChild(addSkillBtn);
                content.appendChild(actions);
            }
            
        } else if (subcategory === "Projects") {
            const projects = projectsData || [];

            const list = document.createElement("ul");
            list.id = `projectsList-${portfolio.id}`;

            const filteredProjects = projects.filter(project => project.portfolio_id === portfolio.id);
            if (filteredProjects.length === 0) {
                const li = document.createElement("li");
                li.textContent = "No projects found.";
                list.appendChild(li);
            } else {
                filteredProjects.forEach((project) => {
                    const li = document.createElement("li");
                    li.id = `projectItem-${project.id}`;

                    const imageHTML = project.image_url ? `<br><img src="api/${project.image_url}" alt="Project Image" style="max-width: 200px; display: block;">` : "";

                    if (portfolio.user_id === loggedInUserId) {

                        const titleLink = document.createElement('a');
                        titleLink.href = "#";
                        titleLink.innerHTML = `<strong>${project.project_name}</strong>`;
                        titleLink.addEventListener('click', () => {
                            editProject(
                                project.id,
                                project.project_name,
                                project.project_url,
                                project.description,
                                portfolio.id,
                                project.image_url || null
                            );
                        });
                        li.appendChild(titleLink);
                        
                        li.appendChild(document.createTextNode(' - '));
                        let url = project.project_url;
                        if (!/^https?:\/\//i.test(url)) {
                            url = 'https://' + url;
                        }
                        const urlLink = document.createElement('a');
                        urlLink.href = project.project_url;
                        urlLink.target = "_blank";
                        urlLink.textContent = project.project_url;
                        li.appendChild(urlLink);
                        
                        li.appendChild(document.createElement('br'));
                        
                        li.appendChild(document.createTextNode(project.description));
                        
                        if (project.image_url) {
                            const img = document.createElement('img');
                            img.src = `api/${project.image_url}`;
                            img.alt = "Project Image";
                            img.style.maxWidth = "100px";
                            img.style.display = "block";
                            img.style.marginTop = "10px";
                            li.appendChild(img);
                        }
                        
                        const delBtn = document.createElement('button');
                        delBtn.textContent = "Delete";
                        delBtn.addEventListener('click', () => {
                            deleteProject(project.id);
                        });
                        li.appendChild(delBtn);
                        
                        list.appendChild(li);                         

                    
  
                        
                    } else {
                        li.innerHTML = `
                            <strong>${project.project_name}</strong> - 
                            <a href="${project.project_url}" target="_blank">${project.project_url}</a><br>
                            ${project.description}
                            ${imageHTML}
                        `;
                    }

                    list.appendChild(li);
                });
            }

            content.appendChild(list);

            if (portfolio.user_id === loggedInUserId) {
                const actions = document.createElement("div");
                actions.classList.add("popup-actions");

                const addProjBtn = document.createElement("button");
                addProjBtn.innerText = "Add";
                addProjBtn.onclick = () => showAddProjectForm(portfolio.id);
                actions.appendChild(addProjBtn);
                content.appendChild(actions);
            }

            
        } else if (subcategory === "About Me") {
            const user = usersData.find(user => user.id === portfolio.user_id);
            const mobile = user.mobile || "No mobile number available";
            const email = user.email || "No email available";
        
            const fullName = `${user.firstname} ${user.middlename} ${user.lastname}`.replace(/\s+/g, ' ').trim();
        
            content.innerHTML = `
                <div class="view-mode" id="aboutMeView">
                    <p><strong>Full Name:</strong> <span id="displayFullName">${fullName}</span></p>
                    <p><strong>Mobile:</strong> <span id="displayMobile">${mobile}</span></p>
                    <p><strong>Email:</strong> <span id="displayEmail">${email}</span></p>
                </div>

                <div class="edit-mode" id="aboutMeEdit" style="display: none;">
                    <p><strong>First Name:</strong> <input type="text" id="ownerFirstname" value="${user.firstname}"></p>
                    <p><strong>Middle Name:</strong> <input type="text" id="ownerMiddlename" value="${user.middlename}"></p>
                    <p><strong>Last Name:</strong> <input type="text" id="ownerLastname" value="${user.lastname}"></p>
                    <p><strong>Mobile:</strong> <input type="text" id="ownerMobile" value="${mobile}"></p>
                </div>

            `;
        
        }

        if (portfolio.user_id === loggedInUserId) {
            const actions = document.createElement("div");
            actions.classList.add("popup-actions");
        
            if (subcategory === "About Me") {
                const editBtn = document.createElement("button");
                editBtn.innerText = "Edit";
                editBtn.classList.add("edit-btn");
            
                editBtn.onclick = () => {
                    const viewSection = document.getElementById("aboutMeView");
                    const editSection = document.getElementById("aboutMeEdit");

                    if (editBtn.innerText === "Edit") {
                        viewSection.style.display = "none";
                        editSection.style.display = "block";
                        editBtn.innerText = "Save";
                    } else {
                        const updatedFirstname = document.getElementById("ownerFirstname").value;
                        const updatedMiddlename = document.getElementById("ownerMiddlename").value;
                        const updatedLastname = document.getElementById("ownerLastname").value;
                        const updatedMobile = document.getElementById("ownerMobile").value;

                        document.getElementById("displayFullName").textContent = `${updatedFirstname} ${updatedMiddlename} ${updatedLastname}`.replace(/\s+/g, ' ').trim();
                        document.getElementById("displayMobile").textContent = updatedMobile;

                        viewSection.style.display = "block";
                        editSection.style.display = "none";
                        editBtn.innerText = "Edit";

                        console.log("User updated to:", {
                            firstname: updatedFirstname,
                            middlename: updatedMiddlename,
                            lastname: updatedLastname,
                            mobile: updatedMobile,
                        });
                        const userId = portfolio.user_id;
                        console.log(userId);
                        saveUpdatedUserDetails(userId);
                    }

                };
            
                actions.appendChild(editBtn);
            }
            
        
            content.appendChild(actions);
        }
        
    }

    newPopup.appendChild(content);
    document.body.appendChild(newPopup);

    dragElement(newPopup);
}


function openEditModal(portfolioId, subcategory) {
    console.log(`Editing portfolio ${portfolioId} in ${subcategory}`);
}


function closePopupSubcategory(closeBtn) {
    const popup = closeBtn.closest('.subcategory-popup');
    if (popup) {
        popup.classList.remove("popup-zoom-in");
        popup.classList.add("popup-zoom-out");

        setTimeout(() => {
            popup.remove();
        }, 300); 
    }
}





// functions api here
function saveNewExperienceEntry(portfolioId, { job_title, company_name, start_date, end_date, description }) {
    return fetch("api/Experience_api.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            portfolio_id: portfolioId,
            job_title: job_title,
            company_name: company_name,
            start_date: start_date,
            end_date: end_date,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => data.status === "success")
    .catch(error => {
        console.error("Error saving experience:", error);
        return false;
    });
}


function saveNewSkillEntry(portfolioId, { skill_name, proficiency }) {
    return fetch("api/Skills_api.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            portfolio_id: portfolioId,
            skill_name: skill_name,
            proficiency: proficiency
        })
    })
    .then(response => response.json())
    .then(data => data.status === "success")
    .catch(error => {
        console.error("Error saving skill:", error);
        return false;
    });
}


function saveNewProjectEntry(portfolioId, { project_name, project_url, description, imageFile }) {
    const formData = new FormData();
    formData.append("portfolio_id", portfolioId);
    formData.append("project_name", project_name);
    formData.append("project_url", project_url);
    formData.append("description", description);
    if (imageFile) {
        formData.append("project_image", imageFile);
    }

    return fetch("api/Projects_api.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => data.status === "success")
    .catch(error => {
        console.error("Error saving project:", error);
        return false;
    });
}




function saveNewEducationEntry(portfolioId, { degree, school, startYear, endYear, description }) {
    return fetch("api/Education_api.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            portfolio_id: portfolioId,
            school_name: school,
            degree: degree,
            start_year: startYear,
            end_year: endYear,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => data.status === "success")
    .catch(error => {
        console.error("Error saving education:", error);
        return false;
    });
}
// Editing Form
function editEducation(id, degree, school, startYear, endYear, description, portfolioId) {

    const form = document.createElement("div");
    form.innerHTML = `
        <input type="text" placeholder="Degree" id="eduDegree" value="${degree}" required>
        <input type="text" placeholder="School" id="eduSchool" value="${school}" required>
        <input type="text" placeholder="Start Year" id="eduStartYear" value="${startYear}" required>
        <input type="text" placeholder="End Year" id="eduEndYear" value="${endYear}" required>
        <textarea placeholder="Description" id="eduDescription">${description}</textarea>
        <button id="updateEduBtn">Update Education</button>
        <button id="cancelEduBtn">Cancel</button>
    `;

    const actions = document.querySelector(".popup-actions");
    actions.appendChild(form);

    document.getElementById("updateEduBtn").onclick = () => {
        const degree = document.getElementById("eduDegree").value.trim();
        const school = document.getElementById("eduSchool").value.trim();
        const startYear = document.getElementById("eduStartYear").value.trim();
        const endYear = document.getElementById("eduEndYear").value.trim();
        const description = document.getElementById("eduDescription").value.trim();

        if (degree && school && startYear && endYear) {
            updateEducationEntry(id, portfolioId, {
                degree, school, startYear, endYear, description
            }).then(updated => {
                if (updated) {
                    alert("Education updated successfully.");
                    const eduItem = document.querySelector(`#educationList-${portfolioId} li[data-id="${id}"]`);
                    eduItem.innerHTML = `
                        <a href="#" onclick="editEducation(${id}, '${degree.replace(/'/g, "\\'")}', '${school.replace(/'/g, "\\'")}', '${startYear}', '${endYear}', '${description.replace(/'/g, "\\'")}')">
                            <strong>${degree}</strong> at ${school}
                        </a> (${startYear}–${endYear})<br>${description}
                        <button onclick="deleteEducation(${id})">Delete</button>
                    `;
                    form.remove();
                } else {
                    alert("Failed to update education.");
                }
            });
        } else {
            alert("All fields except description are required.");
        }
    };

    document.getElementById("cancelEduBtn").onclick = () => form.remove();
}
function editExperience(id, jobTitle, company, startDate, endDate, description, portfolioId) {
    const form = document.createElement("div");
    form.innerHTML = `
        <input type="text" placeholder="Job Title" id="expJobTitle" value="${jobTitle}" required>
        <input type="text" placeholder="Company" id="expCompany" value="${company}" required>
        <input type="text" placeholder="Start Date" id="expStartDate" value="${startDate}" required>
        <input type="text" placeholder="End Date" id="expEndDate" value="${endDate}" required>
        <textarea placeholder="Description" id="expDescription">${description}</textarea>
        <button id="updateExpBtn">Update Experience</button>
        <button id="cancelExpBtn">Cancel</button>
    `;
    const actions = document.querySelector(".popup-actions");
    actions.appendChild(form);

    document.getElementById("updateExpBtn").onclick = () => {
        const jobTitle = document.getElementById("expJobTitle").value.trim();
        const company = document.getElementById("expCompany").value.trim();
        const startDate = document.getElementById("expStartDate").value.trim();
        const endDate = document.getElementById("expEndDate").value.trim();
        const description = document.getElementById("expDescription").value.trim();

        if (jobTitle && company && startDate && endDate) {
            updateExperienceEntry(id, portfolioId, {
                jobTitle, company, startDate, endDate, description
            }).then(updated => {
                if (updated) {
                    alert("Experience updated.");
                    form.remove();
                } else alert("Update failed.");
            });
        } else alert("All fields except description are required.");
    };

    document.getElementById("cancelExpBtn").onclick = () => form.remove();
}
function editSkill(id, skillName, proficiency, portfolioId) {
    const form = document.createElement("div");
    form.innerHTML = `
        <input type="text" placeholder="Skill Name" id="skillName" value="${skillName}" required>
        <input type="number" id="skillProficiency" placeholder="Proficiency (0-100)" min="0" max="100" required>
        <button id="updateSkillBtn">Update Skill</button>
        <button id="cancelSkillBtn">Cancel</button>
    `;
    const actions = document.querySelector(".popup-actions");
    actions.appendChild(form);

    document.getElementById("updateSkillBtn").onclick = () => {
        const skill = document.getElementById("skillName").value.trim();
        const proficiency = document.getElementById("skillProficiency").value.trim();

        if (skill && proficiency ) {
            updateSkillEntry(id, portfolioId, { skill, proficiency  }).then(updated => {
                if (updated) {
                    alert("Skill updated.");
                    form.remove();
                } else alert("Update failed.");
            });
        } else alert("All fields are required.");
    };

    document.getElementById("cancelSkillBtn").onclick = () => form.remove();
}
function editProject(id, name, url, description, portfolioId, image_url = null) {
    const form = document.createElement("div");
    form.innerHTML = `
        <input type="text" placeholder="Project Name" id="projName" value="${name}" required>
        <input type="text" placeholder="Project URL" id="projURL" value="${url}" required>
        <textarea placeholder="Description" id="projDesc">${description}</textarea>

        ${image_url ? `<img src="api/${image_url}" alt="Current Image" style="max-width: 100px; display: block; margin-bottom: 10px;">` : ""}
        <input type="file" id="projImage" accept="image/*">

        <button id="updateProjBtn">Update Project</button>
        <button id="cancelProjBtn">Cancel</button>
    `;

    const actions = document.querySelector(".popup-actions");
    actions.innerHTML = ""; 
    actions.appendChild(form);

    document.getElementById("updateProjBtn").onclick = () => {
        const projectName = document.getElementById("projName").value.trim();
        const projectUrl = document.getElementById("projURL").value.trim();
        const desc = document.getElementById("projDesc").value.trim();
        const imageFile = document.getElementById("projImage").files[0];

        if (projectName && projectUrl) {
            if (imageFile) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    const base64Image = reader.result;
                    sendPutProject(id, projectName, projectUrl, desc, portfolioId, base64Image);
                };
                reader.readAsDataURL(imageFile);
            } else {
                sendPutProject(id, projectName, projectUrl, desc, portfolioId, null);
            }
        } else {
            alert("Project name and URL are required.");
        }
    };

    document.getElementById("cancelProjBtn").onclick = () => {
        form.remove();
    
        const addProjBtn = document.createElement("button");
        addProjBtn.innerText = "Add";
        addProjBtn.onclick = () => {
            showAddProjectForm(portfolioId);
        };
    
        actions.innerHTML = ""; 
        actions.appendChild(addProjBtn);
    };
}
function showAddProjectForm(portfolioId) {
    const actions = document.querySelector(".popup-actions");
    const form = document.createElement("div");
    form.innerHTML = `
        <input type="text" placeholder="Project Name" id="projectName" required>
        <input type="url" placeholder="Project URL" id="projectURL" required>
        <textarea placeholder="Description" id="projectDesc"></textarea>
        <input type="file" id="projectImage" accept="image/*">
        <button id="addProjSubmit">Add Project</button>
        <button id="cancelProjBtn">Dismiss</button>
    `;

    actions.innerHTML = "";
    actions.appendChild(form);

    document.getElementById("addProjSubmit").onclick = async () => {
        const project_name = document.getElementById("projectName").value.trim();
        const project_url = document.getElementById("projectURL").value.trim();
        const description = document.getElementById("projectDesc").value.trim();
        const imageFile = document.getElementById("projectImage").files[0];

        if (project_name && project_url) {
            saveNewProjectEntry(portfolioId, { project_name, project_url, description, imageFile }).then(saved => {
                if (saved) location.reload(); // You could also call openSubcategoryWindow("Projects", portfolioId)
                else alert("Failed to save project.");
            });
        } else {
            alert("Project name and URL are required.");
        }
    };

    document.getElementById("cancelProjBtn").onclick = () => {
        form.remove();

        const addProjBtn = document.createElement("button");
        addProjBtn.innerText = "Add";
        addProjBtn.onclick = () => showAddProjectForm(portfolioId);

        actions.innerHTML = "";
        actions.appendChild(addProjBtn);
    };
}


// Updating
function updateEducationEntry(id, portfolioId, { degree, school, startYear, endYear, description }) {
    return fetch(`api/Education_api.php?id=${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            portfolio_id: portfolioId,
            school_name: school,
            degree: degree,
            start_year: startYear,
            end_year: endYear,
            description: description
        })
    })
    .then(response => response.json())
    .then(responseData => {
        if (responseData.status === 'success') {
            console.log("Education updated successfully.");
            return true;
        } else {
            console.error("Error updating education:", responseData.message);
            return false;
        }
    })
    .catch(error => {
        console.error("Error updating education:", error);
        return false;
    });
}
function updateExperienceEntry(id, portfolioId, { jobTitle, company, startDate, endDate, description }) {
    return fetch(`api/Experience_api.php?id=${id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id, portfolio_id: portfolioId,
            job_title: jobTitle,
            company_name: company,
            start_date: startDate,
            end_date: endDate,
            description
        })
    })
    .then(res => res.json())
    .then(data => data.status === "success")
    .catch(err => { console.error("Update error:", err); return false; });
}

function updateSkillEntry(id, portfolioId, { skill, proficiency  }) {
    return fetch(`api/Skills_api.php?id=${id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id, portfolio_id: portfolioId,
            skill_name: skill,
            proficiency: proficiency 
        })
    })
    .then(res => res.json())
    .then(data => data.status === "success")
    .catch(err => { console.error("Update error:", err); return false; });
}
function updateProjectEntry(id, name, url, description, portfolioId, imageFile = null) {
    const refresh = () => openSubcategoryWindow("Projects", portfolioId); 

    if (imageFile) {
        const reader = new FileReader();
        reader.onloadend = () => {
            sendPutProject(id, name, url, description, portfolioId, reader.result).then(refresh);
        };
        reader.readAsDataURL(imageFile);
    } else {
        sendPutProject(id, name, url, description, portfolioId, null).then(refresh);
    }
}

function sendPutProject(id, name, url, description, portfolioId, base64Image) {
    fetch("api/Projects_api.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id: id,
            portfolio_id: portfolioId,
            project_name: name,
            project_url: url,
            description: description,
            image_base64: base64Image
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        location.reload(); // ✅ Force page reload
    })
    .catch(err => {
        console.error("Project update failed:", err);
    });
}

// Deleting
function deleteEducation(id) {
    if (confirm("Are you sure you want to delete this education record?")) {
        fetch("api/Education_api.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                const li = document.querySelector(`#educationItem-${id}`);
                if (li) li.remove();
            } else {
                alert("Failed to delete education.");
            }
        })
        .catch(error => {
            console.error("Error deleting education:", error);
            alert("An error occurred while deleting the education record.");
        });
    }
}

function deleteExperience(id) {
    if (confirm("Delete this experience?")) {
        fetch("api/Experience_api.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                const li = document.querySelector(`#experienceItem-${id}`);
                if (li) li.remove();
            } else {
                alert("Failed to delete experience.");
            }
        })
        .catch(error => {
            console.error("Error deleting experience:", error);
            alert("An error occurred while deleting the experience.");
        });
    }
}

function deleteSkill(id) {
    if (confirm("Delete this skill?")) {
        fetch("api/Skills_api.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                const li = document.querySelector(`#skillItem-${id}`);
                if (li) li.remove();
            } else {
                alert("Failed to delete skill.");
            }
        })
        .catch(error => {
            console.error("Error deleting skill:", error);
            alert("An error occurred while deleting the skill.");
        });
    }
}

function deleteProject(id) {
    if (confirm("Delete this project?")) {
        fetch("api/Projects_api.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                const li = document.querySelector(`#projectItem-${id}`);
                if (li) li.remove();
            } else {
                alert("Failed to delete project.");
            }
        })
        .catch(error => {
            console.error("Error deleting project:", error);
            alert("An error occurred while deleting the project.");
        });
    }
}


const saveUpdatedUserDetails = (portfolioId) => {
    const updatedFirstname = document.getElementById("ownerFirstname").value;
    const updatedMiddlename = document.getElementById("ownerMiddlename").value;
    const updatedLastname = document.getElementById("ownerLastname").value;
    const updatedMobile = document.getElementById("ownerMobile").value;

    if (!portfolioId) {
        alert("User not found.");
        return;
    }

    fetch('api/User_api.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: portfolioId,  
            firstname: updatedFirstname,
            middlename: updatedMiddlename,
            lastname: updatedLastname,
            mobile: updatedMobile,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const user = usersData.find(u => u.id === portfolioId);
            if (user) {
                user.firstname = updatedFirstname;
                user.middlename = updatedMiddlename;
                user.lastname = updatedLastname;
                user.mobile = updatedMobile;
            }

            document.getElementById("displayFullName").textContent = `${updatedFirstname} ${updatedMiddlename} ${updatedLastname}`.replace(/\s+/g, ' ').trim();
            document.getElementById("displayMobile").textContent = updatedMobile;

            document.getElementById("aboutMeView").style.display = "block";
            document.getElementById("aboutMeEdit").style.display = "none";

            document.querySelector(".edit-btn").innerText = "Edit";

            const portfolioDetailsPopup = document.getElementById("portfolioDetailsPopup");
            if (portfolioDetailsPopup && portfolioDetailsPopup.style.display === "block") {
                const nameElement = document.getElementById("popupName");
                nameElement.textContent = `${updatedFirstname} ${updatedMiddlename} ${updatedLastname}`.replace(/\s+/g, ' ').trim();
                
            }

        } else {
            alert(data.message || 'Failed to update.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating.');
    });
};


