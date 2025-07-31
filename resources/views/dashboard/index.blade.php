@extends('components.dashboard.layouts')


@section('body')

<body>

    <header class="header">
        <div class="header-content">
            <h1> Task Manager</h1>
            <div class="header-actions">
                <button class="btn btn-primary" id="addTaskBtn">
                    ➕ Add Task
                </button>
                <button class="btn btn-secondary" id="logoutBtn">
                    Log out
                </button>
                <h3>
                   Welcome     {{Auth::user()->name}}
                </h3>
            </div>
        </div>
    </header>

    <div class="filters">
        <div class="filters-content">
            <div class="filter-group">
                <label>User</label>
                <select class="filter-select" id="userFilter">
                    <option value="">All Users</option>
                    @foreach ($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select class="filter-select" id="statusFilter">
                    <option value="">All Status</option>
                    @foreach ($statuses as $id => $status)
                        <option value="{{ $id }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Priority</label>
                <select class="filter-select" id="priorityFilter">
                    <option value="">All Priorities</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Due Date</label>
                <input type="date" class="filter-input" id="dueDateFilter">
            </div>
            <button class="btn apply-filters" id="applyFilters">
                <span class="loading"></span>
                Apply Filters
            </button>
            <button class="btn clear-filters" id="clearFilters">Clear Filters</button>
        </div>
    </div>

    <div class="kanban-container">
        <div class="kanban-board" id="kanbanBoard">
        
            @foreach ($statuses as $id => $status )
                 <div class="kanban-column" data-status="todo">
                <div class="column-header">
                    <div class="column-title">
                        {{ ucfirst($status) }}
                        <span class="column-count" id="count" data-id={{$status}}>0</span>
                    </div>
                </div>
                <div class="tasks-container" id="status-{{ $id }}" data-status="{{$status}}" data-id="{{$id}}"></div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="modal" id="taskModal">
        <div class="modal-content">
                    <ul class="alert alert-danger" id="taskError" style="display:none; list-style: none;">
                         
                    </ul>
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add New Task</h2>
                <button class="close-btn" id="closeModal">&times;</button>
            </div>
            <form id="taskForm" onsubmit="saveTask(event)" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Task Title *</label>
                    <input type="text" class="form-input" id="taskTitle" >
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" id="taskDescription" placeholder="Task description..."></textarea>
                </div>
                
                <div class="form-row">

                     <div  class="form-group">
                        <label class="form-label">Image *</label>
                        <input type="file" class="form-input" id="taskImage" accept="image/*">
                        <p style="font-style: bolder; margin-top: 12px;">Previous Image</p>
                        <div class="image-preview" id="imagePreview" style="margin-top: 12px;">
                            <img width="40" height="40" id="previewImage" src="" alt="Task Image Preview" style="display: none;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Priority *</label>
                        <select class="form-select" id="taskPriority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group" id="status-row">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="taskStatus">
                           @foreach ($statuses as $id => $status )
                            <option value="{{$id}}">{{ ucfirst($status) }}</option>
                           @endforeach
                           
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-input" id="taskDueDate">
                    </div>

                   
                </div>
                
                <div class="form-group" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <span id="submitBtnText">Create Task</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="assignRoleModal" class="modal-backdrop">
  <div class="modal-assign">
    <h2>Assign User</h2>
    <label for="user">Select User:</label>
    <select id="user">
      <option value="">-- Select --</option>
  @foreach ($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
    </select>

    <div class="modal-buttons">
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn-save assignUser" >Save</button>
    </div>
  </div>
</div>

</body>

   @include("dashboard.scripts")
@endsection

</html>