<div>
    <style>
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            max-width: 100px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 0;
            color: #000;
        }
    </style>
    <div class="container" style="margin-top:15px;width:100%; height: 85vh; border: 1px solid silver; border-radius: 5px;background-color:white">
        <div class="row">
            <div style="display: flex; justify-content: center; margin-top: 20px;">


                <div class="btn-group" role="group" aria-label="Basic radio toggle button group" style="width: 20%;">
                    <input id="radio1" name="radio1" type="radio" value="Radio1" class="btn-check" />
                    <label for="radio1" class="btn" style="width: 50%; text-align: center; border-top-left-radius: 5px; border-bottom-left-radius: 5px;  border-color: rgb(2, 17, 79); background-color: {{ $activeTab === 'open' ? 'rgb(2, 17, 79)' : 'none' }};  color: {{ $activeTab === 'open' ? '#fff !important' : '#778899' }};" wire:click="$set('activeTab', 'open')">
                        Open
                    </label>

                    <input id="radio2" name="radio1" type="radio" value="Radio2" class="btn-check" />
                    <label for="radio2" class="btn" style="width: 50%; text-align: center; border-color: rgb(2, 17, 79); background-color: {{ $activeTab === 'completed' ? 'rgb(2, 17, 79)' : 'none' }};  color: {{ $activeTab === 'completed' ? '#fff !important' : '#778899' }};" wire:click="$set('activeTab', 'completed')">
                        Closed
                    </label>
                </div>

            </div>
        </div>



        <div style="display: flex; justify-content: center; align-items: center;margin-top:5px">
            @if (session()->has('message'))
            <div id="flash-message" style="width: 90%; margin: 0.2rem; padding: 0.25rem; background-color: #f0fff4; border: 1px solid #68d391; color: #38a169; border-radius: 0.25rem; text-align: center;" class="success-message">
                {{ session('message') }}
            </div>
            @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var flashMessage = document.getElementById('flash-message');
                    if (flashMessage) {
                        flashMessage.style.transition = 'opacity 0.5s ease';
                        flashMessage.style.opacity = '0';
                        setTimeout(function() {
                            flashMessage.remove();
                        }, 500); // Delay to allow the fade-out effect
                    }
                }, 5000); // 5000 milliseconds = 5 seconds
            });
        </script>

        <div style="display:flex; justify-content:flex-end;">
            <button wire:click="show" style="background-color:rgb(2, 17, 79); border: none; border-radius: 5px; color: white; font-size: 12px; height: 30px; cursor: pointer; margin-top: 15px; margin-right: 20px;width:100px;">Add
                New Task</button>
        </div>


        @if ($activeTab == "open")
        <div class="card-body" style="background-color:white;width:100%;margin-top:30px;border-radius:5px;overflow-y:auto;max-height:350px;overflow-x:hidden">
            @if ($records->isEmpty())
            <div style="text-align: center">
                <img style="width: 10em" src="https://media.istockphoto.com/id/1357284048/vector/no-item-found-vector-flat-icon-design-illustration-web-and-mobile-application-symbol-on.jpg?s=612x612&w=0&k=20&c=j0V0ww6uBl1LwQLH0U9L7Zn81xMTZCpXPjH5qJo5QyQ=" alt="">
            </div>
            @else
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: rgb(2, 17, 79); color: white;">
                            <th style="padding: 10px; font-size: 12px; text-align: start; width: 50%">Assignee</th>
                            <th style="padding: 10px; font-size: 12px; text-align: start;width: 30%">Task Name</th>
                            <th style="padding: 10px; font-size: 12px; text-align: center; width: 30%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                        @if($record->status=="Open")
                        <tr>
                            <td colspan="3 m-0 p-0" style="background-color: #fff;padding:10px 0;margin:0;">
                                <div class="accordion m-0 m-auto p-0 border-none" style="width: 100%;  color: #778899;border:none">
                                    <div class="accordion-title p-0 m-0 border-none font-weight-500" onclick="toggleAccordion(this)">
                                        <!-- Content for accordion title -->

                                        <div class="col-6">{{ucwords(strtolower($record->assignee))}}</div>
                                        <div class="col-3 "> {{ ucfirst($record->task_name) }}</div>
                                        <div class="col-3 text-end">@foreach ($record->comments ?? [] as $comment)
                                            {{ $comment->comment }}
                                            @endforeach
                                            <!-- Add Comment link to trigger modal -->
                                            <button type="button" wire:click.prevent="openAddCommentModal('{{ $record->id }}')" class="btn submit-btn btn-sm" data-toggle="modal" data-target="#exampleModalCenter">Add Comment</button>
                                            <button wire:click="openForTasks('{{ $record->id }}')" style="border:1px solid rgb(2, 17, 79);width:80px" class="btn cancel-btn btn-sm">Close</button>
                                        </div>
                                    </div>
                                    <div class="accordion-content" style="display: none;padding:0 10px;">
                                        <!-- Content for accordion body -->
                                        <table class="rounded border" style="margin-top:20px;width: 100%; border-collapse: collapse; ">
                                            <thead class="py-0" style=" background-color: #ecf9ff;    box-shadow: 1px 0px 2px 0px rgba(0, 0, 0, 0.2);border-bottom: 1px solid #ccc;padding:5px;">
                                                <tr style=" color: #778899;">
                                                    <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Priority</th>
                                                    <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Due Date</th>
                                                    <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Subject</th>
                                                    <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Description</th>
                                                    <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Attach</th>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="border:none;width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ $record->priority }}</td>
                                                    <td style="border:none;width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ \Carbon\Carbon::parse($record->due_date)->format('d-M-y') }}</td>
                                                    <td style="border:none;width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ ucfirst($record->subject) }}</td>
                                                    <td style="border:none;width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ ucfirst($record->description) }}</td>
                                                    <td style="border:none;width: 20%;padding: 10px; font-size: 12px; text-align: start;">
                                                        @if ($record->file_path)
                                                        <a href="{{ asset('storage/' . $record->file_path) }}" target="_blank" style="text-decoration: none; color: #007BFF;">View File</a>
                                                        @else
                                                        N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

                <!-- Add Comment Modal -->
                @if($showModal)
                <div wire:ignore.self class="modal fade show" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="display:block;">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #eceff3;">
                                <h6 class="modal-title" id="exampleModalLongTitle">Add Comment</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="closeModal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @if (session()->has('comment_message'))
                            <div class="alert alert-success d-flex justify-content-between align-items-center" role="alert">
                                <span>{{ session('comment_message') }}</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <div class="modal-body">
                                <form wire:submit.prevent="addComment">
                                    <div class="form-group">
                                        <label for="comment" style="color: #778899;font-size:13px;font-weight:500;">Comment:</label>
                                        <p>
                                            <textarea class="form-control" id="comment" wire:model.lazy="newComment" wire:keydown.debounce.500ms="validateField('newComment')"></textarea>
                                        </p>
                                        @error('newComment') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="submit-btn btn-primary btn-sm" style="font-size:12px;">Submit</button>
                                    </div>

                                </form>
                                <div style="max-height: 300px;overflow-y:auto;">
                                    @if ($taskComments->count() > 0)
                                    @foreach($taskComments as $comment)
                                    <div class="comment mb-4 mt-2">
                                        <div class="d-flex align-items-center gap-5">
                                            <div class="col-md-4 p-0 comment-details">
                                                <p style="color: #000;font-size:12px;font-weight:500;margin-bottom:0;" class="truncate-text" title="{{ $comment->employee->full_name }}">{{ $comment->employee->full_name }}
                                                </p>
                                            </div>
                                            <div class=" col-md-3 p-0 comment-time">
                                                <span style="color: #778899;font-size:10px;font-weight:normal;margin-left:15px;">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <!-- <div class=" col-md-2 p-0 comment-actions">
                                                <button class="comment-btn" wire:click="openEditCommentModal({{ $comment->id }})"> <i class="fas fa-edit" style="color: #778899;height:7px;width:7px;"></i></button>
                                                <button class="comment-btn" wire:click="deleteComment({{ $comment->id }})"><i class="fas fa-trash" style="color: #778899;height:7px;width:7px;"></i></button>
                                            </div> -->
                                            @if(Auth::guard('emp')->user()->emp_id == $comment->emp_id)
                                            <div class="col-md-2 p-0 comment-actions">
                                                <button class="comment-btn" wire:click="openEditCommentModal({{ $comment->id }})">
                                                    <i class="fas fa-edit" style="color: #778899;height:7px;width:7px;"></i>
                                                </button>
                                                <button class="comment-btn" wire:click="deleteComment({{ $comment->id }})">
                                                    <i class="fas fa-trash" style="color: #778899;height:7px;width:7px;"></i>
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="col p-0 comment-content">
                                            @if($editCommentId == $comment->id)
                                            <!-- Input field for editing -->
                                            <input class="form-control" wire:model.defer="newComment" type="text">
                                            <!-- Button to update comment -->
                                            <button class="update-btn p-1" wire:click="updateComment({{ $comment->id }})">Update</button>
                                            <button class="btn btn-secondary p-1 m-0" wire:click="cancelEdit" style="font-size: 12px;">Cancel</button>
                                            @else
                                            <!-- Display comment content -->
                                            <p style="margin-bottom: 0;font-size:12px;color:#515963;">{{ ucfirst($comment->comment) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach

                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-backdrop fade show"></div>
                @endif
                @endif
            </div>
            @endif
            @if ($activeTab == "completed")
            <div class="card-body" style="background-color:white;width:100%;margin-top:30px;border-radius:5px;overflow-y:auto;max-height:300px;overflow-x:hidden">

                @if ($records->isEmpty())
                <div style="text-align: center">
                    <img style="width:10em" src="https://media.istockphoto.com/id/1357284048/vector/no-item-found-vector-flat-icon-design-illustration-web-and-mobile-application-symbol-on.jpg?s=612x612&w=0&k=20&c=j0V0ww6uBl1LwQLH0U9L7Zn81xMTZCpXPjH5qJo5QyQ=" alt="">
                </div>
                @else
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: rgb(2, 17, 79); color: white;">
                                <th style="padding: 10px; font-size: 12px; text-align: start;width: 50%">Assignee</th>
                                <th style="padding: 10px; font-size: 12px; text-align: start; width: 30%">Task Name</th>
                                <th style="padding: 10px; font-size: 12px; text-align: center; width: 30%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                            @if($record->status=="Completed")
                            <tr>
                                <td colspan="3 m-0 p-0" style="background-color: #fff;padding:10px 0;margin:0;">
                                    <div class="accordion m-0 m-auto p-0 border-none" style="width: 100%;  color: #778899;border:none">
                                        <div class="accordion-title p-0 m-0 border-none font-weight-500" onclick="toggleAccordion(this)">
                                            <!-- Content for accordion title -->
                                            <div class="col-6">{{ucwords(strtolower($record->assignee))}}</div>
                                            <div class="col-3"> {{ ucfirst($record->task_name) }}</div>
                                            <div class="col-3 text-end">@foreach ($record->comments ?? [] as $comment)
                                                {{ $comment->comment }}
                                                @endforeach
                                                <!-- Add Comment link to trigger modal -->
                                                <button type="button" wire:click.prevent="openAddCommentModal('{{ $record->id }}')" class="btn submit-btn btn-sm" data-toggle="modal" data-target="#exampleModalCenter" style="font-size:12px;">Add Comment</button>
                                                <button wire:click="closeForTasks('{{ $record->id }}')" style="border:1px solid rgb(2,17,79);" class="btn cancel-btn btn-sm">Reopen</button>
                                            </div>
                                        </div>
                                        <div class="accordion-content" style="display: none;">
                                            <!-- Content for accordion body -->
                                            <table style="margin-top:20px;width: 100%; border-collapse: collapse;padding:10px; ">
                                                <thead style=" background-color: #ecf9ff;">
                                                    <tr style=" color: #778899;">
                                                        <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Priority</th>
                                                        <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Due Date</th>
                                                        <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Subject</th>
                                                        <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Description</th>
                                                        <th style="font-weight:500;width: 20%; padding: 10px; font-size: 12px; text-align: start;">Attach</th>
                                                    </tr>

                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ $record->priority }}</td>
                                                        <td style="width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ \Carbon\Carbon::parse($record->due_date)->format('d-M-y') }}</td>
                                                        <td style="width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ ucfirst($record->subject) }}</td>
                                                        <td style="width: 20%;padding: 10px; font-size: 12px; text-align: start;">{{ ucfirst($record->description) }}</td>
                                                        <td style="width: 20%;padding: 10px; font-size: 12px; text-align: start;">
                                                            @if ($record->file_path)
                                                            <a href="{{ asset('storage/' . $record->file_path) }}" target="_blank" style="text-decoration: none; color: #007BFF;">View File</a>
                                                            @else
                                                            N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            @endif
            @if($showDialog)
            <div class="modal" tabindex="-1" role="dialog" style="display: block;">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: rgb(2, 17, 79); color: white; height: 40px; padding: 8px;">
                            <h5 class="modal-title" style="font-size: 15px; margin: 0;"><b>Add Task</b></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="close" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="task-container">
                                <!-- Task Name -->
                                <div class="form-group">
                                    <label for="task_name" style="font-size: 13px;color:#778899;">Task Name*</label>
                                    <br>
                                    <input type="text" wire:model.debounce.0ms="task_name" wire:input="autoValidate" class="placeholder-small" placeholder="Enter task name" style="width: 100%;font-size:12px;padding:5px;outline:none;border:1px solid #ccc;border-radius:5px;">
                                    @error('task_name') <span class="text-danger">Task name is required</span> @enderror
                                </div>

                                <!-- Assignee -->
                                <div class="form-group" style="margin-top: 10px;color:grey;font-size:12px">
                                    <label for="assignee" style="font-size: 13px;color:#778899">Assignee*</label>
                                    <br>
                                    <i wire:change="autoValidate" wire:click="forAssignee" class="fas fa-user icon" id="profile-icon"></i>
                                    @if($showRecipients)
                                    <strong style="font-size: 12;">Selected assignee:
                                    </strong>{{$selectedPeopleName }}
                                    @else
                                    Add Assignee
                                    @endif <br>
                                    @error('assignee') <span class="text-danger">Assignee is required</span> @enderror
                                </div>
                                @if($assigneeList)
                                <div style="border-radius:5px;background-color:grey;padding:8px;width:350px;margin-top:10px;max-height:250px;overflow-y:auto; ">
                                    <div class="input-group" style="margin-bottom: 10px;">
                                        <input wire:input="filter" wire:model.debounce.0ms="searchTerm" style="font-size: 10px;cursor: pointer; border-radius: 5px 0 0 5px;" type="text" class="form-control" placeholder="Search employee name / Id" aria-label="Search" aria-describedby="basic-addon1">
                                        <div class="input-group-append">
                                            <button wire:change="autoValidate" wire:click="filter" style="height: 30px; border-radius: 0 5px 5px 0; background-color: #007BFF; color: #fff; border: none;" class="btn" type="button">
                                                <i style="text-align: center;" class="fa fa-search"></i>
                                            </button>
                                            <button wire:change="autoValidate" wire:click="closeAssignee" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" style="color: white; font-size: 24px;margin-left:2px">×</span>
                                            </button>

                                        </div>
                                    </div>
                                    @if ($peopleData->isEmpty())
                                    <div class="container" style="text-align: center; color: white; font-size: 12px;">No People Found</div>
                                    @else
                                    @foreach($peopleData as $people)
                                    <div wire:click="selectPerson('{{$people->emp_id}}')" class="container" style="cursor: pointer; background-color: darkgrey; padding: 5px; margin-bottom: 8px; width: 300px; border-radius: 5px;">
                                        <div class="row align-items-center">
                                            <label for="person-{{ $people->emp_id }}" style="width: 100%; display: flex; align-items: center; margin: 0;">
                                                <div class="col-auto">
                                                    <input type="radio" id="person-{{ $people->emp_id }}" wire:change="autoValidate" wire:model="assignee" value="{{ $people->emp_id }}">
                                                </div>
                                                <div class="col-auto">
                                                    <img class="profile-image" src="{{
                                                !is_null($people->image) && filter_var($people->image, FILTER_VALIDATE_URL) ? $people->image :
                                                (!empty($people->image) ? Storage::url($people->image) :
                                                ($people->gender == 'Male' ? 'https://www.kindpng.com/picc/m/252-2524695_dummy-profile-image-jpg-hd-png-download.png' :
                                                'https://th.bing.com/th/id/R.f931db21888ef3645a8356047504aa7b?rik=63HALWH%2b%2fKtaNQ&riu=http%3a%2f%2fereadcost.eu%2fwp-content%2fuploads%2f2016%2f03%2fblank_profile_female-7.jpg&ehk=atYRSw0KxmUnhESig51u5yzYBWfaD9KBO5KvdxXRCTY%3d&risl=&pid=ImgRaw&r=0')) }}" alt="">
                                                </div>
                                                <div class="col">
                                                    <h6 class="username" style="font-size: 12px; color: white;">
                                                        {{ ucwords(strtolower($people->first_name)) }} {{ ucwords(strtolower($people->last_name)) }}
                                                    </h6>
                                                    <p class="mb-0" style="font-size: 12px; color: white;">(#{{ $people->emp_id }})</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach

                                    @endif

                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        @if($selectedPersonClients->isEmpty())
                                        @else
                                        <div>
                                            <label style="font-size: 13px;color:#778899" for="clientSelect">Select Client*</label>
                                            <select wire:change="showProjects" style="width: 100%;font-size:12px;padding:5px;outline:none;border:1px solid #ccc;border-radius:5px;" id="clientSelect" wire:model="client_id">
                                                <option value="">Select client</option>
                                                @foreach($selectedPersonClients as $client)
                                                <option style="color:#778899;" value="{{ $client->client->client_id }}">{{ $client->client->client_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('client_id') <span class="text-danger">Client ID is required</span> @enderror
                                        </div>
                                        @endif

                                    </div>
                                    <div class="col-md-6">
                                        @if($selectedPersonClientsWithProjects->isEmpty())
                                        @else
                                        <div>
                                            <label style="font-size: 13px;color:#778899" for="clientSelect">Select Project*</label>
                                            <select wire:change="autoValidate" style="width: 100%;font-size:12px;padding:5px;outline:none;border:1px solid #ccc;border-radius:5px;" id="clientSelect" wire:model="project_name">
                                                <option value="">Select project</option>
                                                @foreach($selectedPersonClientsWithProjects as $project)
                                                <option style="color:#778899;" value="{{ $project->project_name }}">{{ $project->project_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('project_name') <span class="text-danger">Project name is required</span> @enderror

                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="priority-container" style="margin-top: 15px;">
                                    <div class="row ">
                                        <div class="col-md-4">
                                            <label for="priority" style="font-size: 13px;color:#778899; margin-left: 0px; margin-top: 0px; padding: 0 10px 0 0;">Priority</label>
                                        </div>
                                        <div class="col-md-8 mt-1">
                                            <div id="priority" style="display: flex; align-items: center; margin-top: 0px;">
                                                <div class="priority-option" style="margin-left: 0px; padding: 0;">
                                                    <input type="radio" id="low-priority" name="priority" wire:change="autoValidate" wire:model="priority" value="low">
                                                    <span style="font-size: 12px;color:#778899; padding: 0;margin-left:5px" class="text-xs">Low</span>
                                                </div>
                                                <div class="priority-option" style="margin-left: 20px; padding: 0;">
                                                    <input type="radio" id="medium-priority" name="priority" wire:change="autoValidate" wire:model="priority" value="medium">
                                                    <span style="font-size: 12px;color:#778899; padding: 0;margin-left:5px" class="text-xs">Medium</span>
                                                </div>
                                                <div class="priority-option" style="margin-left: 20px; padding: 0;">
                                                    <input type="radio" id="high-priority" name="priority" wire:change="autoValidate" wire:model="priority" value="high">
                                                    <span style="font-size: 12px;color:#778899; padding: 0;margin-left:5px" class="text-xs">High</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Due Date -->
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group" style="margin-top: 20px;">
                                            <label class="form-label" style="font-size: 13px;color:#778899; margin-left: 0px; margin-top: 0px; padding: 0 10px 0 0;">Due Date*</label>
                                            <br>
                                            <input wire:change="autoValidate" type="date" wire:model="due_date" class="placeholder-small" style="width: 100%;font-size:12px;padding:5px;outline:none;border:1px solid #ccc;border-radius:5px;" min="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>">
                                            @error('due_date') <span class="text-danger">Due date is required</span> @enderror

                                        </div>
                                    </div>
                                    <div class="col">
                                        <!-- Tags -->
                                        <div class="form-group" style="margin-top: 20px;">
                                            <label for="tags" style="font-size: 13px;color:#778899; margin-left: 0px; margin-top: 0px; padding: 0 10px 0 0;">Tags</label>
                                            <br>
                                            <input wire:change="autoValidate" type="text" wire:model="tags" placeholder="Enter tags" class="placeholder-small" style="width: 100%;font-size:12px;padding:5px;outline:none;border:1px solid #ccc;border-radius:5px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 10px; color: grey; font-size: 12px">
                                    <label for="assignee" style="font-size: 13px;color:#778899; margin-left: 0px; margin-top: 0px; padding: 0 10px 0 0;">Followers</label>
                                    <br>
                                    <i wire:change="autoValidate" wire:click="forFollowers" class="fas fa-user icon" id="profile-icon"></i>
                                    @if($showFollowers)
                                    <strong style="font-size: 12;">Selected Followers:
                                    </strong>{{ implode(', ', array_unique($selectedPeopleNamesForFollowers)) }}
                                    @else
                                    Add Followers
                                    @endif
                                </div>


                                @if($followersList)
                                <div style="border-radius:5px;background-color:grey;padding:8px;width:350px;margin-top:10px;max-height:250px;overflow-y:auto;">
                                    <div class="input-group" style="margin-bottom: 10px;">
                                        <input wire:input="filter" wire:model.debounce.0ms="searchTerm" style="font-size: 10px;cursor: pointer; border-radius: 5px 0 0 5px;" type="text" class="form-control" placeholder="Search employee name / Id" aria-label="Search" aria-describedby="basic-addon1">
                                        <div class="input-group-append">
                                            <button wire:change="autoValidate" wire:click="filter" style="height: 30px; border-radius: 0 5px 5px 0; background-color: #007BFF; color: #fff; border: none;" class="btn" type="button">
                                                <i style="text-align: center;" class="fa fa-search"></i>
                                            </button>
                                            <button wire:change="autoValidate" wire:click="closeFollowers" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" style="color: white; font-size: 24px;margin-left:2px">×</span>
                                            </button>
                                        </div>
                                    </div>
                                    @if ($peopleData->isEmpty())
                                    <div class="container" style="text-align: center; color: white;font-size:12px"> No
                                        People Found
                                    </div>
                                    @else
                                    @foreach($peopleData as $people)
                                    <div wire:model="cc_to" wire:click="selectPersonForFollowers('{{ $people->emp_id }}')" class="container" style="cursor: pointer; background-color: darkgrey; padding: 5px; margin-bottom: 8px; width: 300px; border-radius: 5px;">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <input type="checkbox" wire:model="selectedPeopleForFollowers" value="{{ $people->emp_id }}">
                                            </div>
                                            <div class="col-auto">
                                                <img class="profile-image" src="{{
                                                            !is_null($people->image) && filter_var($people->image, FILTER_VALIDATE_URL) ? $people->image :
                                                            (!empty($people->image) ? Storage::url($people->image) :
                                                            ($people->gender == 'Male' ? 'https://www.kindpng.com/picc/m/252-2524695_dummy-profile-image-jpg-hd-png-download.png' :
                                                            'https://th.bing.com/th/id/R.f931db21888ef3645a8356047504aa7b?rik=63HALWH%2b%2fKtaNQ&riu=http%3a%2f%2fereadcost.eu%2fwp-content%2fuploads%2f2016%2f03%2fblank_profile_female-7.jpg&ehk=atYRSw0KxmUnhESig51u5yzYBWfaD9KBO5KvdxXRCTY%3d&risl=&pid=ImgRaw&r=0')) }}" alt="">
                                            </div>
                                            <div class="col">
                                                <h6 class="username" style="font-size: 12px; color: white;">
                                                    {{ ucwords(strtolower($people->first_name )) }} {{ ucwords(strtolower($people->last_name )) }}
                                                </h6>
                                                <p class="mb-0" style="font-size: 12px; color: white;">
                                                    (#{{ $people->emp_id }})</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                                @endif
                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="Subject" style="font-size: 13px;color:#778899; margin-left: 0px; margin-top: 0px; padding: 0 10px 0 0;">Subject</label>
                                    <br>
                                    <input wire:change="autoValidate" wire:model="subject" class="placeholder-small" placeholder="Enter subject" rows="4" style="width: 100%;font-size:12px;padding:5px;outline:none;border:1px solid #ccc;border-radius:5px;"></input>
                                </div>
                                <!-- Description -->
                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="description" style="font-size: 13px;color:#778899; margin-left: 0px; margin-top: 0px; padding: 0 10px 0 0;">Description</label>
                                    <br>
                                    <textarea wire:change="autoValidate" wire:model="description" placeholder="Add description" rows="4" style="width: 100%;font-size:12px;padding:5px;outline:none;border:1px solid #ccc;border-radius:5px;"></textarea>
                                </div>

                                <!-- File Input -->
                                <div class="row">
                                    <div class="col">
                                        <label for="fileInput" style="cursor: pointer; font-size: 13px;color:#778899; margin-left: 0px; margin-top: 0px; padding: 0 10px 0 0;">
                                            Attach Image
                                        </label>
                                    </div>
                                </div>


                                <input wire:change="autoValidate" style="font-size: 12px;" wire:model="image" type="file" accept="image/*">

                                <div style="text-align: center;margin-bottom:10px">
                                    <button style="margin-top: 5px;" wire:click="submit" class="submit-btn" type="button" name="link">Save
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show blurred-backdrop"></div>
            @endif
            </body>

        </div>
        <script>
            function toggleAccordion(element) {

                const accordionBody = element.nextElementSibling;

                if (accordionBody.style.display === 'block') {

                    accordionBody.style.display = 'none';

                    element.classList.remove('active'); // Remove active class

                } else {

                    accordionBody.style.display = 'block';

                    element.classList.add('active'); // Add active class

                }
            }
        </script>