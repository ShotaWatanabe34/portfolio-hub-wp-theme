<?php
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: frame-ancestors 'self'");
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Content-Security-Policy" content="default-src 'none'; script-src 'unsafe-inline'; style-src 'unsafe-inline'; connect-src 'none'; img-src 'none'; font-src 'none'; object-src 'none'; base-uri 'none'; form-action 'none'">
<meta name="referrer" content="no-referrer">
<title>MEMO — ブラウザメモ</title>
<style>
:root{color-scheme:dark;--bg:#101b23;--panel:#182832;--line:#334752;--text:#e7eff4;--muted:#adc0ca;--blue:#b7d9ed;--red:#ff8d94}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font:14px/1.6 system-ui,-apple-system,"Yu Gothic",sans-serif}button,input{font:inherit}button{cursor:pointer}button:disabled{cursor:wait;opacity:.6}button:focus-visible,input:focus-visible,textarea:focus-visible,summary:focus-visible{outline:2px solid var(--blue);outline-offset:3px}
.shell{max-width:1500px;margin:auto;padding:26px 32px 16px}.mast{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:20px}.brand{display:flex;gap:14px;align-items:baseline}.brand strong{font-size:25px;letter-spacing:.16em;font-weight:500}.brand span{color:var(--muted);font-size:12px}.privacy{font-size:12px;color:var(--muted);margin:0}.toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px 16px;background:var(--panel);border:1px solid var(--line);border-radius:8px 8px 0 0}.file{display:flex;align-items:center;gap:10px;min-width:0}.file-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:40vw}.state{display:flex;align-items:center;gap:6px;white-space:nowrap;font-size:12px;color:var(--muted)}.dot{display:inline-block;width:10px;height:10px;border:1.5px solid #a9b8c1;border-radius:50%;flex-shrink:0}.dirty .dot{background:var(--red);border-color:var(--red)}.dirty{color:#ffb3b8}.actions{display:flex;align-items:center;gap:16px}.indent-label{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted)}input{accent-color:#a6d2e9}button{border:1px solid #7ca9c0;background:var(--blue);color:#102633;border-radius:5px;padding:7px 20px;font-weight:650;white-space:nowrap}button:hover{background:#d4eaf5}
.editor-frame{display:flex;border:1px solid var(--line);border-top:0;background:#101b23;height:calc(100dvh - 245px);min-height:360px;max-height:1000px;overflow:hidden}.gutter{width:58px;flex-shrink:0;overflow:hidden;user-select:none;color:#708895;background:#13212a;border-right:1px solid #243640;padding:18px 10px;text-align:right;white-space:pre;font:15px/24px Consolas,"SFMono-Regular","Liberation Mono",monospace;margin:0}.editor{display:block;flex:1;min-width:0;border:0;resize:none;background:transparent;color:#eef4f7;padding:18px 20px;margin:0;font:15px/24px Consolas,"SFMono-Regular","Liberation Mono",monospace;tab-size:4;white-space:pre;overflow:auto;caret-color:#c9e9ff;border-radius:0}.editor:focus-visible{outline-offset:-2px}.editor::placeholder{color:#718a99}.statusbar{display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:8px 14px;border:1px solid var(--line);border-top:0;border-radius:0 0 8px 8px;color:var(--muted);font-size:12px;background:var(--panel)}.feedback{min-height:24px;margin:10px 0 4px;font-size:13px;color:var(--blue)}.feedback.error{color:#ffb3b8}.help{color:var(--muted);font-size:12px}.help summary{cursor:pointer;width:max-content}.help p{margin:8px 0}.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);padding:12px 20px;background:#e2edf3;color:#142b38;border-radius:6px;max-width:calc(100% - 32px);box-shadow:0 5px 25px #0006;z-index:5;font-size:13px}.toast[hidden]{display:none}.key{color:#d5e5ee}.footer{margin-top:12px;color:#748d9b;font-size:11px;letter-spacing:.05em}
@media(max-width:650px){.shell{padding:16px 12px}.mast{align-items:flex-start;gap:8px;margin-bottom:14px}.brand{display:block}.brand span{display:block}.privacy{max-width:150px}.toolbar{align-items:flex-start;padding:10px;gap:8px}.file{align-items:flex-start;flex-direction:column;gap:3px}.actions{gap:8px;align-items:flex-end;flex-direction:column}.editor-frame{height:calc(100dvh - 290px);min-height:320px}.gutter{width:40px;padding-inline:5px}.editor{padding-inline:12px}button{padding:6px 16px}.file-name{max-width:45vw}}

body.panel .shell{padding:12px}body.panel .mast,body.panel .footer{display:none}body.panel .editor-frame{height:calc(100dvh - 210px);min-height:180px}body.panel .brand{display:none}

.actions{flex-wrap:wrap;justify-content:flex-end;gap:8px}.actions button{padding:7px 12px}#saveAs{background:transparent;color:var(--text);border-color:var(--line);font-weight:400}#saveAs:hover{background:#29414f}

/* ファイル名と状態、操作ボタンを上下2段で固定。 */
.toolbar{display:flex;flex-direction:column;align-items:stretch;gap:8px;padding:10px 12px}
.file{display:flex;flex-direction:row;align-items:center;gap:8px;width:100%;min-width:0}
.file-name{display:block;flex:1;min-width:0;width:100%;max-width:none;border:1px solid transparent;border-radius:3px;background:transparent;color:var(--text);padding:4px 2px;font:14px/1.5 Consolas,monospace;text-overflow:clip}
.file-name:focus{border-color:#7ca9c0;background:#101b23}
.file .state,.file .indent-label{flex-shrink:0}.file .indent-label{margin-left:2px}
.actions{display:flex;flex-direction:row;align-items:center;justify-content:flex-start;flex-wrap:nowrap;gap:6px;overflow-x:auto;padding:2px 0 4px;width:100%}
.actions button{flex:0 0 auto;font-size:12px;padding:6px 9px;min-height:34px}
#openFile,#clearMemo{background:transparent;color:var(--text);border-color:var(--line);font-weight:400}
#openFile:hover,#clearMemo:hover{background:#29414f}
@media(max-width:380px){.toolbar{padding:8px}.actions{gap:4px}.actions button{padding-inline:6px;font-size:11px}.state{font-size:11px}}
</style>
</head>
<body>
<div class="shell">
<header class="mast"><div class="brand"><strong>MEMO</strong><span>ブラウザメモエディター</span></div><p class="privacy">入力内容は送信しません。<br>この端末内で編集・保存します。</p></header>
<main>
<div class="toolbar">
 <div class="file"><input class="file-name" id="fileName" type="text" value="新しいメモ.txt" readonly aria-label="現在のファイル名。左右矢印キーで名前を確認できます"><span class="state" id="state"><span class="dot" aria-hidden="true"></span><span id="stateText">未保存・空</span></span><label class="indent-label"><input id="indent" type="checkbox" checked>Tab</label></div>
 <div class="actions"><button type="button" id="openFile">ファイルを開く</button><button type="button" id="save">保存</button><button type="button" id="saveAs">名前を付けて保存</button><button type="button" id="clearMemo">クリア</button></div>
 <input id="fileInput" type="file" accept=".txt,text/plain" hidden>
</div>
<div class="editor-frame"><pre class="gutter" id="gutter" aria-hidden="true">1</pre><textarea id="editor" class="editor" aria-label="メモ本文" aria-describedby="keyboardHint" wrap="off" spellcheck="false" autocomplete="off" autocapitalize="off" autocorrect="off" placeholder="ここにメモを入力、またはUTF-8の.txtファイルを1つドロップ…"></textarea></div>
<div class="statusbar"><span id="position">行 1、列 1</span><span id="metrics">0 文字 · 0 bytes · UTF-8 / LF</span></div>
<p id="feedback" class="feedback" role="status" aria-live="polite"></p>
<details class="help"><summary>操作と保存について</summary><p id="keyboardHint"><span class="key">Tab</span>：4列幅のタブ文字を挿入／選択行をインデント。<span class="key">Shift＋Tab</span>：インデント解除。<span class="key">Esc</span>：Tab入力を解除してボタンへ移動可能にします。</p><p><span class="key">Ctrl／⌘＋S</span>：保存。<span class="key">Ctrl／⌘＋Z</span>：元に戻す。<span class="key">Ctrl＋Y／⌘＋Shift＋Z</span>：やり直し。履歴は直近80操作までです。</p><p>初回は保存先を選択し、以後の保存・Ctrl＋Sは同じファイルに上書きします。「名前を付けて保存」またはCtrl／⌘＋Shift＋Sで保存先を変更できます。保存先はブラウザ内に記憶しますが、権限確認が再表示される場合があります。対応するChrome・Edge等とHTTPS環境でお使いください。保存APIに非対応の場合、自動ダウンロードには切り替えません。</p><p>本文は同じブラウザ内に自動保存し、サイトのメモ欄と共有します。ブラウザのデータ削除やプライベートモード終了で失われる場合があるため、必要な文章はファイルにも保存してください。ブラウザ内への自動保存が成功している通常のページ移動では、終了確認を表示しません。ファイル書き込み中や、自動保存に失敗して未保存の変更がある場合は確認します。○は最後にこの画面からファイルへ保存した内容との一致を示し、保存後に他のアプリで行った変更は監視しません。</p><p>UTF-8の.txtファイルを1つ（10MBまで）本文へドロップすると開けます。元の文章がある場合だけ置き換えを確認します。改行はLFで保存します。ドロップ元への書き込みはブラウザの対応と権限に依存します。ファイルへの自動上書きはせず、Ctrl＋Sまたは保存ボタンで書き込みます。</p><p>ファイル名は読み取り専用です。クリックして左右矢印キーやHome・Endで長い名前を確認できます。「クリア」は共有メモと保存先の接続を解除します。元のファイルは削除しません。クリア後の最初の保存では保存先を選び直します。</p><p>文字サイズと書体は固定です。ブラウザの拡大・縮小は利用できます。大量のテキストは端末の動作を遅くする場合があります。</p></details>
</main><footer class="footer">STAGE-A / LOCAL TOOLS · MEMO 1.5</footer>
</div><div id="toast" class="toast" hidden aria-hidden="true"></div>
<script>
(() => {
'use strict';
const $ = id => document.getElementById(id);
const editor=$('editor'), button=$('save'), indent=$('indent');
let savedText=null, saving=false, toastTimer, metricsTimer, composing=false;
let fileHandle=null, loading=false;
function handleDB(mode,handle){return new Promise((resolve,reject)=>{
 const request=indexedDB.open('stage-a-memo-files',1);
 request.onupgradeneeded=()=>request.result.createObjectStore('handles');
 request.onerror=()=>reject(request.error);
 request.onblocked=()=>reject(new Error('保存先の記録がブロックされました'));
 request.onsuccess=()=>{const db=request.result;let result=null;let tx;
 try{tx=db.transaction('handles',mode);const store=tx.objectStore('handles');const op=mode==='readonly'?store.get('current'):store.put(handle,'current');op.onsuccess=()=>{result=op.result;};}
 catch(error){db.close();reject(error);return;}
 tx.oncomplete=()=>{db.close();resolve(result);};tx.onerror=tx.onabort=()=>{db.close();reject(tx.error||new Error('保存先の記録に失敗'));};
 };
});}
let handleReady=handleDB('readonly').then(handle=>{fileHandle=handle||null;}).catch(()=>{});

const DATA='stage-a-memo-v1', ACTIVE='stage-a-memo-active';
const panel=new URLSearchParams(location.search).get('panel')==='1';
if(panel)document.body.classList.add('panel');
let storageFailed=false;
function persist(){
 try{localStorage.setItem(DATA,JSON.stringify({text:editor.value,saved:savedText,name:$('fileName').value}));localStorage.setItem(ACTIVE,'1');storageFailed=false;}
 catch{if(!storageFailed){storageFailed=true;message('ブラウザ内に保存できません。サイトとの同期は利用できないため、ファイルに保存してください。',true);}}
}
function restore(){
 try{const raw=localStorage.getItem(DATA);if(!raw)return;const data=JSON.parse(raw);if(!data||typeof data.text!=='string')return;
 const start=editor.selectionStart,end=editor.selectionEnd;
 editor.value=data.text;savedText=typeof data.saved==='string'?data.saved:null;
 if(typeof data.name==='string')$('fileName').value=data.name;
 editor.setSelectionRange(Math.min(start,data.text.length),Math.min(end,data.text.length));
 }catch{}
}

let history=[{text:'',start:0,end:0}], historyIndex=0;
const snapshot=()=>({text:editor.value,start:editor.selectionStart,end:editor.selectionEnd});
const dirty=()=>savedText===null ? editor.value!=='' : editor.value!==savedText;
function message(text,error=false){$('feedback').textContent=text;$('feedback').classList.toggle('error',error);$('toast').textContent=text;$('toast').hidden=false;clearTimeout(toastTimer);toastTimer=setTimeout(()=>{$('toast').hidden=true;},4500);}
function position(){const prior=editor.value.slice(0,editor.selectionStart);const lines=prior.split('\n');$('position').textContent=`行 ${lines.length}、列 ${Array.from(lines[lines.length-1]).length+1}`;}
function update(){
 $('state').classList.toggle('dirty',dirty());
 $('stateText').textContent=saving?'保存中…':dirty()?'未保存':savedText===null?'未保存・空':'保存済み';
 const count=editor.value.split('\n').length;$('gutter').textContent=Array.from({length:count},(_,i)=>i+1).join('\n');$('gutter').scrollTop=editor.scrollTop;
 position();clearTimeout(metricsTimer);metricsTimer=setTimeout(()=>{$('metrics').textContent=`${Array.from(editor.value).length.toLocaleString()} 文字 · ${new TextEncoder().encode(editor.value).length.toLocaleString()} bytes · UTF-8 / LF`;},150);
}
function remember(){const next=snapshot();if(history[historyIndex].text===next.text)return;history.splice(historyIndex+1);history.push(next);if(history.length>80)history.shift();historyIndex=history.length-1;}
function undo(direction){const next=historyIndex+direction;if(next<0||next>=history.length)return;historyIndex=next;const h=history[next];editor.value=h.text;editor.setSelectionRange(h.start,h.end);persist();update();}
function indentEdit(text,start,end,outdent){
 if(start===end&&!outdent)return {text:text.slice(0,start)+'\t'+text.slice(end),start:start+1,end:start+1};
 const first=start===0?0:text.lastIndexOf('\n',start-1)+1;
 const last=end>start&&text[end-1]==='\n'?end-1:end;
 const boundary=text.indexOf('\n',last);const stop=boundary<0?text.length:boundary;
 const lines=text.slice(first,stop).split('\n');let total=0,firstDelta=0;
 const replacement=lines.map((line,i)=>{let changed;if(outdent){const prefix=line.match(/^(\t| {1,4})/);const removed=prefix?prefix[0].length:0;changed=line.slice(removed);total-=removed;if(i===0)firstDelta=-removed;}else{changed='\t'+line;total++;if(i===0)firstDelta=1;}return changed;}).join('\n');
 return {text:text.slice(0,first)+replacement+text.slice(stop),start:Math.max(first,start+firstDelta),end:Math.max(first,end+total)};
}
editor.addEventListener('beforeinput',event=>{if(event.inputType==='historyUndo'||event.inputType==='historyRedo'){event.preventDefault();undo(event.inputType==='historyUndo'?-1:1);return;}history[historyIndex].start=editor.selectionStart;history[historyIndex].end=editor.selectionEnd;});
editor.addEventListener('input',()=>{if(!composing){remember();persist();}update();});
editor.addEventListener('compositionstart',()=>{composing=true;});editor.addEventListener('compositionend',()=>{composing=false;remember();persist();update();});
editor.addEventListener('scroll',()=>{$('gutter').scrollTop=editor.scrollTop;});
editor.addEventListener('keyup',position);editor.addEventListener('click',position);editor.addEventListener('select',position);
editor.addEventListener('keydown',event=>{
 if(event.isComposing||composing)return;
 const key=event.key.toLowerCase();
 if((event.ctrlKey||event.metaKey)&&!event.altKey){if(key==='z'||key==='y'){event.preventDefault();undo(key==='y'||event.shiftKey?1:-1);return;}}
 if(event.key==='Escape'&&indent.checked){indent.checked=false;message('Tab入力を解除しました。Tabキーで次の操作へ移動できます。');return;}
 if(event.key==='Tab'&&indent.checked&&!event.ctrlKey&&!event.metaKey&&!event.altKey){event.preventDefault();history[historyIndex]=snapshot();const edit=indentEdit(editor.value,editor.selectionStart,editor.selectionEnd,event.shiftKey);editor.value=edit.text;editor.setSelectionRange(edit.start,edit.end);remember();persist();update();}
});
async function save(saveAs=false){
 if(saving||loading)return;
 if(!window.isSecureContext||typeof window.showSaveFilePicker!=='function'){message('このブラウザでは保存先を開けません。HTTPSで対応するChrome・Edge等から再度お試しください。',true);return;}
 const textToSave=editor.value;let writer=null;saving=true;button.disabled=true;$('saveAs').disabled=true;update();
 try{
  await handleReady;
  const handle=(!saveAs&&fileHandle)?fileHandle:await window.showSaveFilePicker({suggestedName:$('fileName').value,types:[{description:'テキストファイル（UTF-8）',accept:{'text/plain':['.txt']}}],excludeAcceptAllOption:true});
  if(handle.queryPermission&&await handle.queryPermission({mode:'readwrite'})!=='granted'){if(await handle.requestPermission({mode:'readwrite'})!=='granted')throw new Error('書き込み権限がありません');}
  writer=await handle.createWritable();await writer.write(new Blob([textToSave],{type:'text/plain;charset=utf-8'}));await writer.close();writer=null;
  fileHandle=handle;let remembered=true;try{await handleDB('readwrite',handle);}catch{remembered=false;}
  savedText=textToSave;$('fileName').value=handle.name;persist();message(dirty()?'保存しました。保存開始後の変更が未保存で残っています。':'保存しました。');if(!remembered)message('保存しました。保存先を記憶できないため、再読み込み後は保存先の選択が必要です。');
 }catch(error){if(writer){try{await writer.abort();}catch{}}message(error&&error.name==='AbortError'?'保存をキャンセルしました。':'保存できませんでした。保存先の権限や空き容量を確認してください。',!(error&&error.name==='AbortError'));}
 finally{saving=false;button.disabled=false;$('saveAs').disabled=false;update();}
}
// All file-opening routes share validation and save-target handling.
async function openText(handlePromise,fallback){
 if(saving||loading){message('処理中です。完了後にファイルを開いてください。',true);return;}
 loading=true;button.disabled=true;$('saveAs').disabled=true;editor.readOnly=true;
 try{
  const handle=await handlePromise;
  if(handle&&handle.kind!=='file')throw new Error('フォルダは開けません。.txtファイルを選んでください。');
  const file=handle?await handle.getFile():fallback;
  if(!file||! /\.txt$/i.test(file.name))throw new Error('UTF-8の.txtファイルのみ開けます。');
  if(file.size>10*1024*1024)throw new Error('開けるテキストファイルは10MBまでです。');
  let text;
  try{text=new TextDecoder('utf-8',{fatal:true}).decode(await file.arrayBuffer());}catch{throw new Error('UTF-8として読み込めません。文字コードをUTF-8にして保存してください。');}
  if(text.includes('\0'))throw new Error('テキストとして扱えないデータが含まれています。');
  text=text.replace(/\r\n?/g,'\n');
  if(editor.value&&editor.value!==text&&!window.confirm('現在のメモを、ドロップしたファイルの内容に置き換えますか？必要な内容は先に保存してください。'))return;
  await handleReady;
  // Clear the old target even on browsers that provide only a read-only File.
  await handleDB('readwrite',handle||null);
  fileHandle=handle||null;editor.value=text;savedText=text;$('fileName').value=file.name;
  editor.setSelectionRange(0,0);history=[snapshot()];historyIndex=0;persist();update();
  message(handle?'ファイルを開きました。Ctrl＋Sでこのファイルへ上書きできます。':'ファイルを開きました。このブラウザではドロップ元への直接保存に対応していないため、初回の保存時に保存先を選んでください。');
 }catch(error){message(error.message||'ファイルを開けませんでした。',true);}
 finally{loading=false;button.disabled=false;$('saveAs').disabled=false;editor.readOnly=false;}
 }
function receiveFiles(entries){
 if(entries.length!==1){message('テキストファイルを1つだけ選んでください。',true);return Promise.resolve();}
 return openText(entries[0].handle,entries[0].file);
}
window.stageMemoReceiveFiles=receiveFiles;
function hasFiles(event){return event.dataTransfer&&([...event.dataTransfer.types].includes('Files')||event.dataTransfer.files.length);}
function collectFiles(data){
 const items=[...(data.items||[])].filter(item=>item.kind==='file');
 if(items.length)return items.map(item=>({handle:typeof item.getAsFileSystemHandle==='function'?item.getAsFileSystemHandle().catch(()=>null):Promise.resolve(null),file:item.getAsFile()}));
 return [...data.files].map(file=>({handle:Promise.resolve(null),file}));
}
document.addEventListener('dragover',event=>{if(hasFiles(event)){event.preventDefault();event.dataTransfer.dropEffect='copy';}},true);
document.addEventListener('drop',event=>{if(!hasFiles(event))return;event.preventDefault();event.stopPropagation();return receiveFiles(collectFiles(event.dataTransfer));},true);
$('openFile').addEventListener('click',async()=>{
 if(saving||loading)return;
 if(window.isSecureContext&&typeof window.showOpenFilePicker==='function'){
  try{const handles=await window.showOpenFilePicker({multiple:false,types:[{description:'UTF-8テキスト',accept:{'text/plain':['.txt']}}],excludeAcceptAllOption:true});await openText(Promise.resolve(handles[0]),null);}
  catch(error){message(error.name==='AbortError'?'読み込みをキャンセルしました。':'ファイルを選択できませんでした。',error.name!=='AbortError');}
 }else{$('fileInput').click();}
});
$('fileInput').addEventListener('change',async()=>{const files=[...$('fileInput').files];if(files.length)await receiveFiles(files.map(file=>({handle:Promise.resolve(null),file})));$('fileInput').value='';});
$('clearMemo').addEventListener('click',async()=>{
 if(saving||loading){message('処理が完了してからクリアしてください。',true);return;}
 if(!window.confirm('メモ本文と保存先の接続をリセットしますか？別タブ・サイドバーの共有メモも空になります。保存済みのファイルは削除されません。'))return;
 loading=true;$('clearMemo').disabled=true;editor.readOnly=true;
 try{
  await handleReady;
  await handleDB('readwrite',null);
  localStorage.setItem(DATA,JSON.stringify({text:'',saved:null,name:'新しいメモ.txt'}));
  fileHandle=null;savedText=null;editor.value='';$('fileName').value='新しいメモ.txt';$('fileInput').value='';
  history=[{text:'',start:0,end:0}];historyIndex=0;storageFailed=false;
  editor.scrollTop=0;editor.scrollLeft=0;editor.setSelectionRange(0,0);update();message('クリアしました。新しいメモとして使えます。');
 }catch{message('リセットできませんでした。ブラウザの保存領域を確認してください。',true);}
 finally{loading=false;$('clearMemo').disabled=false;editor.readOnly=false;editor.focus();}
});
button.addEventListener('click',()=>save(false));
$('saveAs').addEventListener('click',()=>save(true));
document.addEventListener('keydown',event=>{if((event.ctrlKey||event.metaKey)&&event.key.toLowerCase()==='s'&&!event.altKey&&!event.isComposing){event.preventDefault();save(event.shiftKey);}});
window.addEventListener('beforeunload',event=>{if(composing)persist();if(saving||(dirty()&&storageFailed)){event.preventDefault();event.returnValue='';}});
if(!window.isSecureContext||typeof window.showSaveFilePicker!=='function'){$('feedback').textContent='保存先の選択には、HTTPSと対応ブラウザが必要です。この環境では保存機能を利用できません。';$('feedback').classList.add('error');}
restore();history=[snapshot()];historyIndex=0;
try{localStorage.setItem(ACTIVE,'1');}catch{}
window.addEventListener('storage',event=>{
 if(event.key!==DATA)return;
 handleReady=handleDB('readonly').then(handle=>{fileHandle=handle||null;}).catch(()=>{});
 if(composing)return;
 restore();history=[snapshot()];historyIndex=0;update();
});
update();
})();
</script>
</body></html>
