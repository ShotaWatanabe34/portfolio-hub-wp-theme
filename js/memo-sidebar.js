(() => {
'use strict';
const button=document.getElementById('memo-menu'),panel=document.getElementById('memo-sidebar'),close=document.getElementById('memo-close'),frame=document.getElementById('memo-frame');
if(!button||!panel||!frame||!close)return;
const OPEN_KEY='stage-a-memo-sidebar-open';
function available(){try{button.hidden=localStorage.getItem('stage-a-memo-active')!=='1';}catch{button.hidden=true;}}
function toggle(open,restore=false){if(!restore){try{sessionStorage.setItem(OPEN_KEY,open?'1':'0');}catch{}}panel.hidden=!open;document.body.classList.toggle('memo-is-open',open);button.setAttribute('aria-expanded',String(open));if(open){if(!frame.getAttribute('src'))frame.src=frame.dataset.src;if(!restore)close.focus();}else if(!restore)button.focus();}
button.addEventListener('click',()=>toggle(panel.hidden));close.addEventListener('click',()=>toggle(false));
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!panel.hidden)toggle(false);});
// Prevent browser navigation when files land on the sidebar header or site.
function hasFiles(event){return event.dataTransfer&&([...event.dataTransfer.types].includes('Files')||event.dataTransfer.files.length);}
document.addEventListener('dragover',event=>{if(!panel.hidden&&hasFiles(event)){event.preventDefault();event.dataTransfer.dropEffect='copy';}},true);
document.addEventListener('drop',event=>{
 if(panel.hidden||!hasFiles(event))return;
 event.preventDefault();event.stopPropagation();
 const items=[...(event.dataTransfer.items||[])].filter(item=>item.kind==='file');
 const entries=items.length?items.map(item=>({handle:typeof item.getAsFileSystemHandle==='function'?item.getAsFileSystemHandle().catch(()=>null):Promise.resolve(null),file:item.getAsFile()})):[...event.dataTransfer.files].map(file=>({handle:Promise.resolve(null),file}));
 function deliver(){try{if(typeof frame.contentWindow.stageMemoReceiveFiles==='function'){frame.contentWindow.stageMemoReceiveFiles(entries);return true;}}catch{}return false;}
 if(!deliver())frame.addEventListener('load',()=>{if(!deliver())window.alert('メモを更新して「ファイルを開く」ボタンから選択してください。');},{once:true});
},true);
function restoreState(){available();try{toggle(!button.hidden&&sessionStorage.getItem(OPEN_KEY)==='1',true);}catch{}}
window.addEventListener('storage',available);window.addEventListener('pageshow',restoreState);window.addEventListener('focus',available);restoreState();
})();
