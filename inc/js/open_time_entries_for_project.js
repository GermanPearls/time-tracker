//Open list of time entries for project
function open_time_entries_for_project(projectName) {
    project = encodeURIComponent(projectName);
    window.location.href = '/time-tracker/time-log/?project=' + project;
}