let cur = +localStorage.ch || 0, last = 0;
const $ = s => document.querySelector(s), esc = s => s.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const api = (a, o) => fetch('api.php?' + a, o).then(r => r.json());
const post = (a, d) => api(a, {method: 'POST', body: new URLSearchParams(d)});

async function channels() {
  const list = await api('a=channels');
  if (!list.find(c => c.id == cur)) cur = list[0].id;
  $('#chs').innerHTML = list.map(c => `<a data-id="${c.id}" class="${c.id == cur ? 'on' : ''}"># ${esc(c.name)}</a>`).join('');
  $('#head').textContent = '# ' + list.find(c => c.id == cur).name;
}
async function poll(reset) {
  if (reset) { $('#msgs').innerHTML = ''; last = 0; }
  const c = cur, ms = await api(`a=messages&c=${c}&after=${last}`);
  if (c != cur || !ms.length) return;
  const box = $('#msgs'), atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 60 || reset;
  box.insertAdjacentHTML('beforeend', ms.map(m =>
    `<div class="m"><b>${esc(m.user)}</b><small>${new Date(m.ts*1000).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</small><div>${esc(m.body)}</div></div>`).join(''));
  last = ms[ms.length - 1].id;
  if (atBottom) box.scrollTop = box.scrollHeight;
}
async function open(id) { cur = id; localStorage.ch = id; await channels(); poll(true); }
$('#chs').onclick = e => e.target.dataset.id && open(+e.target.dataset.id);
$('#send').onsubmit = async e => {
  e.preventDefault(); const i = e.target.body;
  if (!i.value.trim()) return;
  await post('a=send', {c: cur, body: i.value}); i.value = ''; poll();
};
$('#nc').onsubmit = async e => {
  e.preventDefault(); const r = await post('a=newchannel', {name: e.target.name.value});
  if (r.error) return alert(r.error);
  e.target.reset(); open(r.id);
};
open(cur); setInterval(() => poll(), 1500); setInterval(channels, 10000);
