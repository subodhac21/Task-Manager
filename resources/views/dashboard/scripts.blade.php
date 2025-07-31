<script>
    let tasks = [];
    let filteredTasks = [];

    let editingTaskId = null;
    let filters = {
        user: '',
        status: '',
        priority: '',
        dueDate: ''
    };

    const taskModal = document.getElementById('taskModal');
    const taskForm = document.getElementById('taskForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtnText');

    document.getElementById('addTaskBtn').addEventListener('click', () => openTaskModal());
    document.getElementById('closeModal').addEventListener('click', () => closeTaskModal());
    taskModal.addEventListener('click', (e) => {
        if (e.target.id === 'taskModal') closeTaskModal();
    });


    document.getElementById('userFilter').addEventListener('change', (e) => {
        filters.user = e.target.value;
        applyFilters();
    });

    document.getElementById('statusFilter').addEventListener('change', (e) => {
        filters.status = e.target.value;
        applyFilters();
    });

    document.getElementById('priorityFilter').addEventListener('change', (e) => {
        filters.priority = e.target.value;
        applyFilters();
    });

    document.getElementById('dueDateFilter').addEventListener('change', (e) => {
        filters.dueDate = e.target.value;
        applyFilters();
    });

    document.getElementById('clearFilters').addEventListener('click', clearFilters);

    function setupSortable() {
        const containers = document.querySelectorAll('.tasks-container');

        containers.forEach(container => {
            new Sortable(container, {
                group: 'shared',
                animation: 300,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onStart: (evt) => {
                    evt.item.classList.add('dragging');
                },
                onEnd: (evt) => {
                    evt.item.classList.remove('dragging');
                    const taskId = evt.item.dataset.taskid;
                    const newStatusId = evt.to.dataset.id;
                    updateTaskStatus(taskId, newStatusId);
                }
            });
        });
    }


    function openTaskModal(task = null) {
        if (task) {
            editingTaskId = task.id;
            modalTitle.textContent = 'Edit Task';
            submitBtn.textContent = 'Update Task';

            document.getElementById('taskTitle').value = task.title;
            document.getElementById('taskDescription').value = task.description || '';
            document.getElementById('taskPriority').value = task.priority;
            document.getElementById('taskStatus').value = task.status;
            document.getElementById("previewImage").src = task.image_path || '';
            document.getElementById("previewImage").style.display = 'block';
            document.getElementById("status-row").style.display = 'none';
        } else {
            editingTaskId = null;
            modalTitle.textContent = 'Add New Task';
            submitBtn.textContent = 'Create Task';
            taskForm.reset();
        }

        taskModal.classList.add('show');
    }

    function closeTaskModal() {
        taskModal.classList.remove('show');
        editingTaskId = null;
    }

    async function saveTask(event) {
        event.preventDefault();
        let taskError = document.getElementById('taskError');
        taskError.style.display = 'none';

        const formData = new FormData();
        formData.append('title', document.getElementById('taskTitle').value.trim());
        formData.append('description', document.getElementById('taskDescription').value.trim());
        formData.append('priority', document.getElementById('taskPriority').value);
        formData.append('status_id', document.getElementById("taskStatus").value);

        const imageFile = document.getElementById('taskImage').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        const url = editingTaskId ?
            "{{ route('tasks.update.details', ':id') }}".replace(':id', editingTaskId) :
            "{{ route('tasks.store') }}";

        await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(async (response) => {
                if (!response.ok) {
                    const errorData = await response.json();
                    throw errorData;
                }
                closeTaskModal();

                return response.json();
            })
            .then((data) => {
                renderTasks();
            })
            .catch((error) => {
                taskError.style.display = 'block';
                let errors = "";
                Object.values(error.errors || {}).forEach(err => {
                    errors += `<li>${err.join(', ')}</li>`;
                });
                taskError.innerHTML = `<ul>${errors}</ul>`;
            });


    }

    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            tasks = tasks.filter(task => task.id !== taskId);
            renderTasks();
        }
    }

    async function updateTaskStatus(taskId, newStatus) {
        const task = filteredTasks.find(t => t.id == taskId);
        console.log(filteredTasks, newStatus)
        if (task) {
            await fetch("{{ route('tasks.update', ':id') }}".replace(':id', taskId), {
                method: 'PUT',
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    status_id: newStatus
                })
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).then(data => {
                task.status = data.status;
            }).catch(error => {
                console.error('Error updating task status:', error);
            });
            {{-- task.status = newStatus; --}}
            renderTasks();
        }
    }

    function applyFilters() {
        {{-- renderTasks(); --}}
    }

    function clearFilters() {
        filters = {
            user: '',
            status: '',
            priority: '',
            dueDate: ''
        };

        document.getElementById('userFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('priorityFilter').value = '';
        document.getElementById('dueDateFilter').value = '';

        renderTasks();
    }

    {{-- function getFilteredTasks() {
        return tasks.filter(task => {
            if (filters.user && task.assigned_to !== filters.user) return false;
            if (filters.status && task.status !== filters.status) return false;
            if (filters.priority && task.priority !== filters.priority) return false;
            if (filters.dueDate && task.dueDate !== filters.dueDate) return false;
            return true;
        });
    } --}}

    function getDueDateClass(dueDate) {
        if (!dueDate) return '';

        const today = new Date();
        const due = new Date(dueDate);
        const diffTime = due - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) return 'overdue';
        if (diffDays <= 3) return 'due-soon';
        return '';
    }

    function formatDate(dateString) {
        if (!dateString) return 'No due date';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    function createTaskCard(task) {
        const dueDateClass = getDueDateClass(task.dueDate);

        return `
            <div class="task-card priority-${task.priority}" data-taskid="${task.id}">
                <div class="task-actions">
                    <button class="task-action-btn" onclick="openTaskModal(filteredTasks.find(t => t.id == '${task.id}'))" title="Edit">
                        ✏️
                    </button>
                    <button class="task-action-btn" onclick="deleteTask('${task.id}')" title="Delete">
                        🗑️
                    </button>
                     @if (Auth::user()->hasRole('admin'))
                    <button  data-id=${task.id} class="assign-role">Assign User</button>
                @endif
                </div>
                <div class="task-title">${task.title}</div>
                ${task.description ? `<div class="task-description">${task.description}</div>` : ''}
                <div class="task-meta">
                    <span class="task-assignee">Assigned to: ${task.assignee.name ?? 'not assigned'}</span>
                </div>
                <img width="80" height="80" src="${task.image }" alt="Task Image" class="task-image">
            </div>
        `;
    }

    async function renderTasks(params = null) {
        let statuses = [];
        const query = new URLSearchParams(params).toString();
        await fetch(params ? '{{ route('tasks.index', ':id') }}'.replace(":id", `${query}`) :
                '{{ route('tasks.index') }}', {
                    method: 'GET',
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    }
                })
            .then(response => response.json())
            .then(data => {
                tasks = data.data.map(task => ({
                    ...task,
                    createdAt: new Date(task.createdAt)
                }));

                statuses = data.data.map((task) => {
                    return task?.status?.name
                })

                filteredTasks = tasks;
            })
            .catch(error => console.error('Error fetching tasks:', error));

        const taskConainer = document.querySelectorAll(".tasks-container");

        taskConainer.forEach(container => {
            container.innerHTML = '';
        });


        const statusContainers = {};

        taskConainer.forEach((item) => {
            let status = item.dataset.status;
            statusContainers[status] = item;
        })

        const tasksByStatus = statuses.reduce((acc, status) => {
            acc[status] = [];
            return acc;
        }, {});




        filteredTasks.forEach(task => {
            if (tasksByStatus[task.status.name]) {
                tasksByStatus[task.status.name].push(task);
            }
        });



        Object.keys(tasksByStatus).forEach(status => {
            const container = statusContainers[status];
            const statusTasks = tasksByStatus[status];

            container.innerHTML = statusTasks.length === 0 ?
                '<div class="empty-state">No tasks</div>' :
                statusTasks.map(task => createTaskCard(task)).join('');

            const countElement = document.getElementById(`${status.replace('-', '')}Count`);
            if (countElement) {
                countElement.textContent = statusTasks.length;
            }
        });

        let activeTaskId = null;

// Attach to all role buttons
document.querySelectorAll(".assign-role").forEach((role) => {
  role.addEventListener("click", (e) => {
    activeTaskId = role.dataset.id;
    document.getElementById("assignRoleModal").style.display = "flex";
  });
});

document.querySelector(".assignUser").addEventListener("click", async () => {
  const user = document.querySelector("#user").value;

  if (!user) {
    alert("Please select a user");
    return;
  }

  try {
    const response = await fetch("{{route('tasks.assign.user')}}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content
      },
      body: JSON.stringify({
        task_id: activeTaskId,
        user_id: user,
      }),
    });

    const data = await response.json();

    if (data?.success) {
      alert(data.message);
      document.getElementById("assignRoleModal").style.display = "none";
      location.reload()
    } else {
      alert("Assignment failed.");
    }
  } catch (err) {
    console.error("Error:", err);
  }
});


        
    }

    setupSortable();
    renderTasks();

    function closeModal() {
        document.getElementById('assignRoleModal').style.display = 'none';
    }



    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeTaskModal();
        }

        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            openTaskModal();
        }
    });


    function filterTasks() {
        const userFilter = document.getElementById('userFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const priorityFilter = document.getElementById('priorityFilter').value;
        const assigned_at = document.getElementById('dueDateFilter').value;



        renderTasks({
            'assigned_to': userFilter,
            'status': statusFilter,
            'priority': priorityFilter,
            'assigned_at': assigned_at
        });
    }

    document.getElementById('applyFilters').addEventListener('click', filterTasks);
</script>


<script>
    const logoutBtn = document.getElementById('logoutBtn');

    logoutBtn.addEventListener('click', async () => {
        if (confirm('Are you sure you want to log out?')) {
            await fetch("{{ route('logout') }}", {
                    method: 'POST',
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    window.location.href = '/';
                })
                .catch(error => {
                    console.error('Error logging out:', error);
                });
        }
    });
</script>
