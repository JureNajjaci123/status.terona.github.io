async function testFetch() {
    try {
        const response = await fetch('https://jsonplaceholder.typicode.com/posts');
        const data = await response.json();
        console.log('Fetch test successful:', data);
    } catch (error) {
        console.error('Fetch test failed:', error);
    }
}

testFetch();
async function checkStatus(url) {
    try {
        console.log('Fetching URL:', url); // Beleženje URL-ja
        const response = await fetch(url, { method: 'GET' });
        console.log('Response Status:', response.status); // Beleženje statusa odgovora
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return true;
    } catch (error) {
        console.error('Error checking status:', error);
        return false;
    }
}
