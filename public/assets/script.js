let tableBody = document.getElementById('followups')

const followUps = async () => {
    tableBody.innerHTML = "<tr><td colspan='7'>Loading...</td></tr>"
    const response = await fetch('/api/followups')

    const data = await response.json()

    if (!response.ok) {
        throw new Error(data.error)
    }

    tableBody.innerHTML = ''

    if (data.data.length === 0) {
        tableBody.innerHTML = '<td colspan="7">Not Data</td>'
        return
    }

    data.data.forEach(item => {
        let tr = document.createElement('tr')

        const values = [
            item.id,
            item.customer_id,
            item.title,
            item.priority,
            item.status,
            item.due_at,
            item.creator_id,
        ];

        values.forEach(value => {
            const td = document.createElement('td')
            td.textContent = value
            tr.appendChild(td)
        })

        const actionTd = document.createElement('td');
        const deleteBtn = document.createElement('button');
        deleteBtn.style.backgroundColor = 'green'
        deleteBtn.style.color = 'white'
        deleteBtn.style.border = 'none'
        deleteBtn.style.borderRadius = '2px'
        deleteBtn.textContent = 'Complete';

        deleteBtn.addEventListener('click', async () => {
            if (!confirm('Are you sure you want to complete this follow-up?')) {
                return;
            }

            const response = await fetch(`/api/followups/${item.id}/complete`, {
                method: 'POST',
                headers: {
                    'X-User-Id': '2'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error);
            }

            alert('Follow-up Complete successfully!');
            await followUps();
        });

        const ReopenTd = document.createElement('td');
        const ReopenBtn = document.createElement('button');
        ReopenBtn.style.backgroundColor = 'red'
        ReopenBtn.style.color = 'white'
        ReopenBtn.style.border = 'none'
        ReopenBtn.style.borderRadius = '2px'
        ReopenBtn.textContent = 'Reopen';

        ReopenBtn.addEventListener('click', async () => {
            if (!confirm('Are you sure you want to complete this follow-up?')) {
                return;
            }

            const response = await fetch(`/api/followups/${item.id}/reopen`, {
                method: 'POST',
                headers: {
                    'X-User-Id': '2'
                }
            })
            const data = await response.json()

            if (!response.ok) {
                throw new Error(data.error)
            }

            alert('Follow-up Reopen successfully!')
            await followUps()
        })

        ReopenTd.appendChild(ReopenBtn)
        tr.appendChild(ReopenTd)

        actionTd.appendChild(deleteBtn);
        tr.appendChild(actionTd);
        tableBody.appendChild(tr)
    })
}

followUps()

const createForm = document.getElementById('createForm');
const modal = document.getElementById('createModal');
const openModal = document.getElementById('openModal');
const cancelModal = document.getElementById('cancelModal');

openModal.addEventListener('click', () => {
    modal.style.display = 'flex';
});

cancelModal.addEventListener('click', () => {
    modal.style.display = 'none';
});

createForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(createForm);

    const data = {
        customer_id: Number(formData.get('customer_id')),
        title: formData.get('title'),
        description: formData.get('description'),
        priority: formData.get('priority'),
        due_at: formData.get('due_at').replace('T', ' ') + ':00'
    };

    const response = await fetch('/api/followups', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-User-Id': '2'
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();

    if (!response.ok) {
        alert(result.error)
        throw new Error(result.error || 'Failed to create follow-up');
    }

    alert('Follow-up created successfully!');

    createForm.reset();
    modal.style.display = 'none';

    await followUps();
});