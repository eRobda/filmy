var Movies = (function() {
    async function GetByName(name) {
        const url = 'https://corsproxy.io/?' + encodeURIComponent("https://prehrajto.cz/hledej/" + name);
        const className = 'video--link';

        try {
            // Fetch the content of the URL
            const response = await fetch(url);
            const html = await response.text();

            // Parse the HTML using DOMParser
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Find the first element with the specified class
            const elements = doc.querySelectorAll(`.${className}`);

            let links = [];
            // Extract the link (assuming it's an <a> tag)
            elements.forEach(x => {
                const link = x ? x.href : 'Link not found';
                let newUrl = link.replace("http://localhost:63342", "https://prehrajto.cz");
                links.push(newUrl);
            });
            return links[0];

            /*const response2 = await fetch('https://corsproxy.io/?' + encodeURIComponent(links[0]));
            const html2 = await response2.text();
            const parser2 = new DOMParser();
            const doc2 = parser2.parseFromString(html2, "text/html");
            return doc2.querySelectorAll("#content_video_html5_api")[0].src;*/
        } catch (error) {
            console.error('Error fetching the video link:', error);
            document.getElementById('video-link').innerText = 'Error fetching the video link';
        }
    }

    return {
        GetByName: GetByName
    };
})();
