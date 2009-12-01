function chPostAvatar(pAvaImg) {
	if (pAvaImg.value == 'no_avatar.png')
		document.getElementById('postavatar').src = gkl_site + '/wp-content/plugins/post-avatar/images/no_avatar.png';
	else
		document.getElementById('postavatar').src = gkl_avatar + pAvaImg.value;

	return true;
}