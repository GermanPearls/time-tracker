//Start logging time for a particular task

function start_timer_for_task(client, ticket) {
    client = encodeURIComponent(client);
    ticket = encodeURIComponent(ticket);
    window.location.href = '/time-tracker/new-time-entry/?client-name=' + client + '&task-name=' + ticket;
}